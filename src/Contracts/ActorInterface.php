<?php

namespace MVF\Codeception\UseCases\Contracts;

interface ActorInterface
{
    public function wantTo($text);
    public function markTestIncomplete($message = "");
    public function fail($message = "");

    /**
     * Returns an array that describes what columns in the table uniquely identify rows of that table, e.g.
     *
     * If our list of $uniqueIdentifiers contains
     *
     * $uniqueIdentifiers = [
     *      SubcategoryTable::getTableName() => [SubcategoryTable::id()],
     *      PendingInvoiceBillingDataTable::getTableName() => [
     *          PendingInvoiceBillingDataTable::pendingInvoiceId(),
     *          PendingInvoiceBillingDataTable::billingDataId(),
     *      ],
     * ];
     *
     * Then
     *
     * $uniqueIdentifiers[SubcategoryTable::getTableName()];
     *
     * Would return ['sugarcrm.zz_subcategories.id'] which identifies that 'sugarcrm.zz_subcategories.id' column is
     * enough to uniquely identify 'sugarcrm.zz_subcategories' rows, on the other hand
     *
     * $uniqueIdentifiers[PendingInvoiceBillingDataTable::getTableName()];
     *
     * would return [
     *      'sugarcrm.pending_invoice_billing_data.pending_invoice_id',
     *      'sugarcrm.pending_invoice_billing_data.billing_data_id',
     * ]
     *
     * this would mean that both 'sugarcrm.pending_invoice_billing_data.pending_invoice_id' and
     * 'sugarcrm.pending_invoice_billing_data.billing_data_id' together can be used to uniquely identify rows in
     * 'sugarcrm.pending_invoice_billing_data' table.
     *
     * @param string $databaseAndTable
     * @return string[]
     */
    public function getKeyIdentifier(string $databaseAndTable): array;
}
