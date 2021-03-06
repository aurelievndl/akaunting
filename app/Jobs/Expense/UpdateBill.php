<?php

namespace App\Jobs\Expense;

use App\Abstracts\Job;
use App\Events\Expense\BillUpdated;
use App\Events\Expense\BillUpdating;
use App\Models\Expense\Bill;
use App\Models\Expense\BillTotal;
use App\Traits\Currencies;
use App\Traits\DateTime;
use App\Traits\Relationships;

class UpdateBill extends Job
{
    use Currencies, DateTime, Relationships;

    protected $bill;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @param  $request
     */
    public function __construct($bill, $request)
    {
        $this->bill = $bill;
        $this->request = $this->getRequestInstance($request);
    }

    /**
     * Execute the job.
     *
     * @return Bill
     */
    public function handle()
    {
        if (empty($this->request['amount'])) {
            $this->request['amount'] = 0;
        }

        event(new BillUpdating($this->bill, $this->request));

        // Upload attachment
        if ($this->request->file('attachment')) {
            $media = $this->getMedia($this->request->file('attachment'), 'bills');

            $this->bill->attachMedia($media, 'attachment');
        }

        $this->createItemsAndTotals();

        $bill_paid = $this->bill->paid;

        unset($this->bill->reconciled);

        if (($bill_paid) && $this->request['amount'] > $bill_paid) {
            $this->request['bill_status_code'] = 'partial';
        }

        $this->bill->update($this->request->input());

        $this->bill->updateRecurring();

        event(new BillUpdated($this->bill));

        return $this->bill;
    }

    protected function createItemsAndTotals()
    {
        // Create items
        list($sub_total, $taxes) = $this->createItems();

        // Delete current totals
        $this->deleteRelationships($this->bill, 'totals');

        $sort_order = 1;

        // Add sub total
        BillTotal::create([
            'company_id' => $this->bill->company_id,
            'bill_id' => $this->bill->id,
            'code' => 'sub_total',
            'name' => 'bills.sub_total',
            'amount' => $sub_total,
            'sort_order' => $sort_order,
        ]);

        $this->request['amount'] += $sub_total;

        $sort_order++;

        // Add discount
        if (!empty($this->request['discount'])) {
            $discount_total = $sub_total * ($this->request['discount'] / 100);

            BillTotal::create([
                'company_id' => $this->bill->company_id,
                'bill_id' => $this->bill->id,
                'code' => 'discount',
                'name' => 'bills.discount',
                'amount' => $discount_total,
                'sort_order' => $sort_order,
            ]);

            $this->request['amount'] -= $discount_total;

            $sort_order++;
        }

        // Add taxes
        if (!empty($taxes)) {
            foreach ($taxes as $tax) {
                BillTotal::create([
                    'company_id' => $this->bill->company_id,
                    'bill_id' => $this->bill->id,
                    'code' => 'tax',
                    'name' => $tax['name'],
                    'amount' => $tax['amount'],
                    'sort_order' => $sort_order,
                ]);

                $this->request['amount'] += $tax['amount'];

                $sort_order++;
            }
        }

        // Add extra totals, i.e. shipping fee
        if (!empty($this->request['totals'])) {
            foreach ($this->request['totals'] as $total) {
                $total['company_id'] = $this->bill->company_id;
                $total['bill_id'] = $this->bill->id;
                $total['sort_order'] = $sort_order;

                if (empty($total['code'])) {
                    $total['code'] = 'extra';
                }

                BillTotal::create($total);

                if (empty($total['operator']) || ($total['operator'] == 'addition')) {
                    $this->request['amount'] += $total['amount'];
                } else {
                    // subtraction
                    $this->request['amount'] -= $total['amount'];
                }

                $sort_order++;
            }
        }

        // Add total
        BillTotal::create([
            'company_id' => $this->bill->company_id,
            'bill_id' => $this->bill->id,
            'code' => 'total',
            'name' => 'bills.total',
            'amount' => $this->request['amount'],
            'sort_order' => $sort_order,
        ]);
    }

    protected function createItems()
    {
        $sub_total = 0;

        $taxes = [];

        if (empty($this->request['items'])) {
            return [$sub_total, $taxes];
        }

        // Delete current items
        $this->deleteRelationships($this->bill, ['items', 'item_taxes']);

        foreach ((array) $this->request['items'] as $item) {
            if (empty($item['discount'])) {
                $item['discount'] = !empty($this->request['discount']) ? !empty($this->request['discount']) : 0;
            }

            $bill_item = $this->dispatch(new CreateBillItem($item, $this->bill));

            // Calculate totals
            $sub_total += $bill_item->total;

            if (!$bill_item->item_taxes) {
                continue;
            }

            // Set taxes
            foreach ((array) $bill_item->item_taxes as $item_tax) {
                if (array_key_exists($item_tax['tax_id'], $taxes)) {
                    $taxes[$item_tax['tax_id']]['amount'] += $item_tax['amount'];
                } else {
                    $taxes[$item_tax['tax_id']] = [
                        'name' => $item_tax['name'],
                        'amount' => $item_tax['amount']
                    ];
                }
            }
        }

        return [$sub_total, $taxes];
    }
}
