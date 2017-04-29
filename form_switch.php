<?php
/*******************************************************************************
 MLInvoice: web-based invoicing application.
 Copyright (C) 2010-2017 Ere Maijala

 Portions based on:
 PkLasku : web-based invoicing software.
 Copyright (C) 2004-2008 Samu Reinikainen

 This program is free software. See attached LICENSE.

 *******************************************************************************/

/*******************************************************************************
 MLInvoice: web-pohjainen laskutusohjelma.
 Copyright (C) 2010-2017 Ere Maijala

 Perustuu osittain sovellukseen:
 PkLasku : web-pohjainen laskutusohjelmisto.
 Copyright (C) 2004-2008 Samu Reinikainen

 Tämä ohjelma on vapaa. Lue oheinen LICENSE.

 *******************************************************************************/
require_once 'settings.php';

$strListTableAlias = '';
$strOrder = '';
$levelsAllowed = [
    ROLE_USER,
    ROLE_BACKUPMGR
];
$copyLinkOverride = '';
$strJSONType = '';
$clearRowValuesAfterAdd = false;
$onAfterRowAdded = '';
$readOnlyForm = false;
$addressAutocomplete = false;
$formDataAttrs = [];
if (!isset($strFunc)) {
    $strFunc = '';
}
if (!isset($strList)) {
    $strList = '';
}

switch ($strForm) {

case 'company' :
    $strTable = '{prefix}company';
    $strJSONType = 'company';
    $strParentKey = 'company_id';
    $addressAutocomplete = true;
    $astrSearchFields = [
        [
            'name' => 'company_name',
            'type' => 'TEXT'
        ]
    ];

    $defaultCustomerNr = FALSE;
    if (getSetting('add_customer_number')) {
        $strQuery = 'SELECT max(customer_no) FROM {prefix}company WHERE deleted=0';
        $intRes = mysqli_query_check($strQuery);
        $defaultCustomerNr = mysqli_fetch_value(mysqli_query_check($strQuery)) + 1;
    }

    $astrFormElements = [
        [
            'name' => 'company_name',
            'label' => $GLOBALS['locClientName'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1
        ],
        [
            'name' => 'inactive',
            'label' => $GLOBALS['locClientInactive'],
            'type' => 'CHECK',
            'style' => 'medium',
            'position' => 2,
            'default' => 0,
            'allow_null' => true
        ],
        [
            'name' => 'company_id',
            'label' => $GLOBALS['locClientVATID'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'email',
            'label' => $GLOBALS['locEmail'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'customer_no',
            'label' => $GLOBALS['locCustomerNr'],
            'type' => 'INT',
            'style' => 'medium',
            'position' => 1,
            'default' => $defaultCustomerNr,
            'allow_null' => true
        ],
        [
            'name' => 'default_ref_number',
            'label' => $GLOBALS['locCustomerDefaultRefNr'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'org_unit_number',
            'label' => $GLOBALS['locOrgUnitNumber'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'payment_intermediator',
            'label' => $GLOBALS['locPaymentIntermediator'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'delivery_terms_id',
            'label' => $GLOBALS['locDeliveryTerms'],
            'type' => 'LIST',
            'style' => 'medium',
            'listquery' => 'SELECT id, name FROM {prefix}delivery_terms WHERE deleted=0 ORDER BY order_no;',
            'position' => 1,
            'default' => null,
            'allow_null' => true
        ],
        [
            'name' => 'delivery_method_id',
            'label' => $GLOBALS['locDeliveryMethod'],
            'type' => 'LIST',
            'style' => 'medium',
            'listquery' => 'SELECT id, name FROM {prefix}delivery_method WHERE deleted=0 ORDER BY order_no;',
            'position' => 2,
            'default' => null,
            'allow_null' => true
        ],
        [
            'name' => 'payment_days',
            'label' => $GLOBALS['locPaymentDays'],
            'type' => 'INT',
            'style' => 'short',
            'position' => 1,
            'default' => null,
            'allow_null' => true
        ],
        [
            'name' => 'terms_of_payment',
            'label' => $GLOBALS['locTermsOfPayment'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'default' => null,
            'allow_null' => true
        ],
        [
            'name' => 'street_address',
            'label' => $GLOBALS['locStreetAddr'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'zip_code',
            'label' => $GLOBALS['locZipCode'],
            'type' => 'TEXT',
            'style' => 'short',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'city',
            'label' => $GLOBALS['locCity'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'country',
            'label' => $GLOBALS['locCountry'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'phone',
            'label' => $GLOBALS['locPhone'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'fax',
            'label' => $GLOBALS['locFAX'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'gsm',
            'label' => $GLOBALS['locGSM'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'www',
            'label' => $GLOBALS['locWWW'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'billing_address',
            'label' => $GLOBALS['locBillAddr'],
            'type' => 'AREA',
            'style' => 'medium',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'info',
            'label' => $GLOBALS['locInfo'],
            'type' => 'AREA',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'tags',
            'label' => $GLOBALS['locTags'],
            'type' => 'TAGS',
            'style' => 'noemptyvalue long',
            'listquery' => 'table=company_tag&sort=tag',
            'position' => 0,
            'default' => null,
            'allow_null' => true
        ],
        [
            'name' => 'company_contacts',
            'label' => $GLOBALS['locContacts'],
            'type' => 'IFORM',
            'style' => 'full',
            'position' => 0,
            'allow_null' => true,
            'parent_key' => 'company_id'
        ]
    ];
    break;

case 'company_contact' :
case 'company_contacts' :
    $strTable = '{prefix}company_contact';
    $strJSONType = 'company_contact';
    $strParentKey = 'company_id';
    $clearRowValuesAfterAdd = true;
    $astrFormElements = [
        [
            'name' => 'contact_type',
            'label' => $GLOBALS['locContactType'],
            'type' => 'LIST',
            'style' => 'medium',
            'listquery' => [
                'invoice' => $GLOBALS['locContactTypeInvoice'],
                'dispatch' => $GLOBALS['locContactTypeDispatchNote'],
                'receipt' => $GLOBALS['locContactTypeReceipt'],
                'order_confirmation' => $GLOBALS['locContactTypeOrderConfirmation'],
                'reminder' => $GLOBALS['locContactTypeReminder'],
                'offer' => $GLOBALS['locContactTypeOffer']
            ],
            'position' => 0,
            'allow_null' => true
        ],
        [
            'name' => 'id',
            'label' => '',
            'type' => 'HID_INT',
            'style' => 'medium',
            'position' => 0
        ],
        [
            'name' => 'contact_person',
            'label' => $GLOBALS['locContactPerson'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 0
        ],
        [
            'name' => 'person_title',
            'label' => $GLOBALS['locPersonTitle'],
            'type' => 'TEXT',
            'style' => 'short',
            'listquery' => '',
            'position' => 0,
            'allow_null' => true
        ],
        [
            'name' => 'phone',
            'label' => $GLOBALS['locPhone'],
            'type' => 'TEXT',
            'style' => 'small',
            'position' => 0,
            'allow_null' => true
        ],
        [
            'name' => 'gsm',
            'label' => $GLOBALS['locGSM'],
            'type' => 'TEXT',
            'style' => 'small',
            'position' => 0,
            'allow_null' => true
        ],
        [
            'name' => 'email',
            'label' => $GLOBALS['locEmail'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 0,
            'allow_null' => true
        ],
        [
            'name' => 'tags',
            'label' => $GLOBALS['locTags'],
            'type' => 'TAGS',
            'style' => 'noemptyvalue medium',
            'listquery' => 'table=contact_tag&sort=tag',
            'position' => 0,
            'default' => null,
            'allow_null' => true
        ]
    ];
    break;

case 'product' :
    $strTable = '{prefix}product';
    $strJSONType = 'product';
    $astrSearchFields = [
        [
            'name' => 'product_name',
            'type' => 'TEXT'
        ]
    ];

    if (sesWriteAccess()) {
        $locStockBalanceChange = $GLOBALS['locStockBalanceChange'];
        $locStockBalanceChangeDescription = $GLOBALS['locStockBalanceChangeDescription'];
        $locUpdateStockBalance = $GLOBALS['locUpdateStockBalance'];
        $locSave = $GLOBALS['locSave'];
        $locClose = $GLOBALS['locClose'];
        $locTitle = $GLOBALS['locUpdateStockBalance'];
        $locMissing = $GLOBALS['locErrValueMissing'];
        $locDecimalSeparator = $GLOBALS['locDecimalSeparator'];
        $popupHTML = <<<EOS
<script type="text/javascript" src="js/stock_balance.js"></script>
<div id="update_stock_balance" class="form_container ui-widget-content" style="display: none">
  <div class="medium_label">$locStockBalanceChange</div> <div class="field"><input type='TEXT' id="stock_balance_change" class='short'></div>
  <div class="medium_label">$locStockBalanceChangeDescription</div> <div class="field"><textarea id="stock_balance_change_desc" class="large"></textarea></div>
  </div>
EOS;

        $updateStockBalanceCode = <<<EOS
<a class="formbuttonlink" href="#" onclick="update_stock_balance({'save': '$locSave', 'close': '$locClose', 'title': '$locTitle', 'missing': '$locMissing: ', 'decimal_separator': '$locDecimalSeparator'})">$locUpdateStockBalance</a>

EOS;
    }

    $barcodeTypes = [
        'EAN13' => 'EAN13',
        'C39' => 'CODE 39',
        'C39E' => 'CODE 39 Extended',
        'C128' => 'CODE 128',
        'C128A' => 'CODE 128 A',
        'C128B' => 'CODE 128 B',
        'C128C' => 'CODE 128 C'
    ];

    $astrFormElements = [
        [
            'name' => 'order_no',
            'label' => $GLOBALS['locOrderNr'],
            'type' => 'INT',
            'style' => 'short',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'product_code',
            'label' => $GLOBALS['locProductCode'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'product_name',
            'label' => $GLOBALS['locProductName'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1
        ],
        [
            'name' => 'product_group',
            'label' => $GLOBALS['locProductGroup'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'vendor',
            'label' => $GLOBALS['locProductVendor'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'vendors_code',
            'label' => $GLOBALS['locProductVendorsCode'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'barcode1',
            'label' => $GLOBALS['locFirstBarcode'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'barcode1_type',
            'label' => $GLOBALS['locBarcodeType'],
            'type' => 'LIST',
            'style' => 'medium',
            'position' => 2,
            'listquery' => $barcodeTypes,
            'allow_null' => true
        ],
        [
            'name' => 'barcode2',
            'label' => $GLOBALS['locSecondBarcode'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'barcode2_type',
            'label' => $GLOBALS['locBarcodeType'],
            'type' => 'LIST',
            'style' => 'medium',
            'position' => 2,
            'listquery' => $barcodeTypes,
            'allow_null' => true
        ],
        [
            'name' => 'description',
            'label' => $GLOBALS['locProductDescription'],
            'type' => 'TEXT',
            'style' => 'long',
            'position' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'internal_info',
            'label' => $GLOBALS['locInternalInfo'],
            'type' => 'AREA',
            'style' => 'xlarge',
            'position' => 0,
            'allow_null' => true
        ],
        [
            'name' => 'unit_price',
            'label' => $GLOBALS['locUnitPrice'],
            'type' => 'INT',
            'style' => 'medium',
            'position' => 1,
            'decimals' => getSetting('unit_price_decimals'),
            'allow_null' => true
        ],
        [
            'name' => 'type_id',
            'label' => $GLOBALS['locUnit'],
            'type' => 'LIST',
            'style' => 'short translated',
            'listquery' => 'SELECT id, name FROM {prefix}row_type WHERE deleted=0 ORDER BY order_no;',
            'position' => 2,
            'default' => 'POST'
        ],
        [
            'name' => 'price_decimals',
            'label' => $GLOBALS['locPriceInvoiceDecimals'],
            'type' => 'INT',
            'style' => 'short',
            'position' => 1,
            'default' => 2
        ],
        [
            'name' => 'discount',
            'label' => $GLOBALS['locDiscountPercent'],
            'type' => 'INT',
            'style' => 'percent',
            'position' => 2,
            'decimals' => 1,
            'allow_null' => true
        ],
        [
            'name' => 'vat_percent',
            'label' => $GLOBALS['locVATPercent'],
            'type' => 'INT',
            'style' => 'short',
            'position' => 1,
            'default' => getSetting('invoice_default_vat_percent'),
            'decimals' => 1
        ],
        [
            'name' => 'vat_included',
            'label' => $GLOBALS['locVATIncluded'],
            'type' => 'CHECK',
            'style' => 'medium',
            'position' => 2,
            'default' => FALSE,
            'allow_null' => true
        ],
        [
            'name' => 'purchase_price',
            'label' => $GLOBALS['locPurchasePrice'],
            'type' => 'INT',
            'style' => 'medium',
            'position' => 1,
            'decimals' => getSetting('unit_price_decimals'),
            'allow_null' => true
        ],
        [
            'name' => 'stock_balance',
            'label' => $GLOBALS['locStockBalance'],
            'type' => 'INT',
            'style' => 'small',
            'position' => 2,
            'decimals' => 2,
            'allow_null' => true,
            'read_only' => true,
            'attached_elem' => $updateStockBalanceCode
        ]
    ];
    break;

case 'invoice' :
    $levelsAllowed[] = ROLE_READONLY;
    $strTable = '{prefix}invoice';
    $strListTableAlias = 'i.'; // this is for the search function
    $strParentKey = 'invoice_id';
    $strJSONType = 'invoice';
    $addressAutocomplete = true;
    $defaultState = 1;
    $isOffer = false;

    $arrRefundedInvoice = [
        'allow_null' => true
    ];
    $arrRefundingInvoice = [
        'allow_null' => true
    ];
    $intInvoiceId = getRequest('id', 0);
    if ($intInvoiceId) {
        $isOffer = isOffer($intInvoiceId);
        $strQuery = 'SELECT refunded_invoice_id ' . 'FROM {prefix}invoice ' .
             'WHERE id=?'; // ok to maintain links to deleted invoices too
        $intRes = mysqli_param_query($strQuery,
            [
                $intInvoiceId
            ]);
        $strBaseLink = '?' . preg_replace('/&id=\d*/', '', $_SERVER['QUERY_STRING']);
        $strBaseLink = preg_replace('/&/', '&amp;', $strBaseLink);
        if ($intRes) {
            $intRefundedInvoiceId = mysqli_fetch_value($intRes);
            if ($intRefundedInvoiceId)
                $arrRefundedInvoice = [
                    'name' => 'get',
                    'label' => $GLOBALS['locShowRefundedInvoice'],
                    'type' => 'BUTTON',
                    'style' => 'custom',
                    'listquery' => "$strBaseLink&amp;id=$intRefundedInvoiceId",
                    'position' => 2,
                    'allow_null' => true
                ];
        }
        $strQuery = 'SELECT id ' . 'FROM {prefix}invoice ' .
             'WHERE deleted=0 AND refunded_invoice_id=?';
        $intRes = mysqli_param_query($strQuery,
            [
                $intInvoiceId
            ]);
        if ($intRes && ($row = mysqli_fetch_assoc($intRes))) {
            $intRefundingInvoiceId = $row['id'];
            if ($intRefundingInvoiceId)
                $arrRefundingInvoice = [
                    'name' => 'get',
                    'label' => $GLOBALS['locShowRefundingInvoice'],
                    'type' => 'BUTTON',
                    'style' => 'custom',
                    'listquery' => "'$strBaseLink&amp;id=$intRefundingInvoiceId",
                    'position' => 2,
                    'allow_null' => true
                ];
        }
    } else {
        if (getRequest('offer', false)) {
            $defaultState = getInitialOfferState();
            $isOffer = true;
        }
    }

    $companyOnChange = '';
    $getInvoiceNr = '';
    $updateDates = '';
    $addCompanyCode = '';
    $addPartialPaymentCode = '';

    if (sesWriteAccess()) {
        $locUpdateDates = $GLOBALS['locUpdateDates'];
        $updateDates = <<<EOS
<a class="formbuttonlink" href="#" onclick="$.getJSON('json.php?func=get_invoice_defaults', {id: $('#record_id').val(), invoice_no: $('#invoice_no').val(), invoice_date: $('#invoice_date').val(), base_id: $('#base_id').val(), company_id: $('#company_id').val(), interval_type: $('#interval_type').val()}, function(json) { $('#invoice_date').val(json.date); $('#due_date').val(json.due_date); $('#next_interval_date').val(json.next_interval_date); $('.save_button').addClass('ui-state-highlight'); }); return false;">$locUpdateDates</a>
EOS;

        $locNew = $GLOBALS['locNew'] . '...';
        $locClientName = $GLOBALS['locClientName'];
        $locEmail = $GLOBALS['locEmail'];
        $locPhone = $GLOBALS['locPhone'];
        $locAddress = $GLOBALS['locStreetAddr'];
        $locZip = $GLOBALS['locZipCode'];
        $locCity = $GLOBALS['locCity'];
        $locCountry = $GLOBALS['locCountry'];
        $locSave = $GLOBALS['locSave'];
        $locClose = $GLOBALS['locClose'];
        $locTitle = $GLOBALS['locNewClient'];
        $locMissing = $GLOBALS['locErrValueMissing'];
        $addCompanyCode = <<<EOS
<a class="formbuttonlink" href="#" onclick="add_company({'save': '$locSave', 'close': '$locClose', 'title': '$locTitle', 'missing': '$locMissing: '})">$locNew</a>

EOS;

        $popupHTML = <<<EOS
<div id="quick_add_company" class="form_container ui-widget-content" style="display: none">
  <div class="medium_label">$locClientName</div> <div class="field"><input type='TEXT' id="quick_name" class='medium'></div>
  <div class="medium_label">$locEmail</div> <div class="field"><input type='TEXT' id="quick_email" class='medium'></div>
  <div class="medium_label">$locPhone</div> <div class="field"><input type='TEXT' id="quick_phone" class='medium'></div>
  <div class="medium_label">$locAddress</div> <div class="field"><input type='TEXT' id="quick_street_address" class='medium'></div>
  <div class="medium_label">$locZip</div> <div class="field"><input type='TEXT' id="quick_zip_code" class='medium'></div>
  <div class="medium_label">$locCity</div> <div class="field"><input type='TEXT' id="quick_city" class='medium'></div>
  <div class="medium_label">$locCountry</div> <div class="field"><input type='TEXT' id="quick_country" class='medium'></div>
</div>

EOS;

        if (!$isOffer) {
            $companyOnChange = '_onChangeCompany';

            $getInvoiceNr = <<<EOS
$.getJSON('json.php?func=get_invoice_defaults', {id: $('#record_id').val(), invoice_no: $('#invoice_no').val(), invoice_date: $('#invoice_date').val(), base_id: $('#base_id').val(), company_id: $('#company_id').val(), interval_type: $('#interval_type').val()}, function(json) { $('#invoice_no').val(json.invoice_no); $('#ref_number').val(json.ref_no); $('.save_button').addClass('ui-state-highlight'); }); return false;
EOS;

            $addPartialPaymentCode = <<<EOS
add_partial_payment({'save': '$locSave', 'close': '$locClose', 'title': '{$GLOBALS['locPartialPayment']}', 'missing': '$locMissing: ', 'partial_payment': '{$GLOBALS['locPartialPayment']}', 'decimal_separator': '{$GLOBALS['locDecimalSeparator']}'}); return false;

EOS;

            $popupHTML .= <<<EOS
<div id="add_partial_payment" class="form_container ui-widget-content" style="display: none">
  <div class="medium_label">{$GLOBALS['locPaymentAmount']}</div> <div class="field"><input type='TEXT' id="add_partial_payment_amount" class='medium'></div>
  <div class="medium_label">{$GLOBALS['locPayDate']}</div> <div class="field"><input type='TEXT' id="add_partial_payment_date" class='date hasCalendar'></div>
</div>

EOS;

            if (getSetting('invoice_warn_if_noncurrent_date')) {
                $formDataAttrs[] = 'check-invoice-date';
            }

            if (!getSetting('invoice_add_number')) {
                $formDataAttrs[] = 'check-invoice-number';
            }
        }
    }

    if (sesWriteAccess() && !$isOffer) {
        $today = dateConvDBDate2Date(date('Ymd'));
        $markPaidToday = <<<EOS
if ([1, 2, 5, 6, 7].indexOf(parseInt($('#state_id').val())) !== -1) { $('#state_id').val(3); } if (!$(this).is('#payment_date')) { $('#payment_date').val('$today'); }
EOS;
        if (getSetting('invoice_auto_archive')) {
            $markPaidToday .= <<<EOS
if ($('#interval_type').val() == 0) { $('#archived').prop('checked', true); }
EOS;
        }
        $markPaidToday .= <<<EOS
$('.save_button').addClass('ui-state-highlight'); return false;
EOS;
        $markPaidTodayButton = '<a class="formbuttonlink" href="#" onclick="' .
             $markPaidToday . '">' . $GLOBALS['locMarkAsPaidToday'] . '</a>';
        if (getSetting('invoice_mark_paid_when_payment_date_set')) {
            $markPaidTodayEvent = <<<EOF
if ($(this).val()) { $markPaidToday }
EOF;
        } else {
            $markPaidTodayEvent = '';
        }
    } else {
        $markPaidTodayEvent = '';
        $markPaidTodayButton = '';
    }

    // Print buttons
    $printButtons = [];
    $printButtons2 = [];
    $res = mysqli_param_query(
        'SELECT * FROM {prefix}print_template WHERE deleted=0 and type=? and inactive=0 ORDER BY order_no',
        [$isOffer ? 'offer' : 'invoice']
    );
    $templateCount = mysqli_num_rows($res);
    $templateFirstCol = 3;
    $rowNum = 0;
    while ($row = mysqli_fetch_assoc($res)) {
        $templateId = $row['id'];
        $printStyle = $row['new_window'] ? 'openwindow' : 'redirect';

        if (sesWriteAccess()) {
            $printFunc = "MLInvoice.printInvoice('$templateId', '$strFunc', '$printStyle'); return false;";
        } else {
            // Check if this print template is safe for read-only use
            $printer = instantiateInvoicePrinter($row['filename']);
            if (!$printer->getReadOnlySafe()) {
                continue;
            }

            if ($printStyle == 'openwindow')
                $printFunc = "window.open('invoice.php?id=_ID_&amp;template=$templateId&amp;func=$strFunc'); return false;";
            else
                $printFunc = "window.location = 'invoice.php?id=_ID_&amp;template=$templateId&amp;func=$strFunc'; return false;";
        }

        $arr = [
            'name' => "print$templateId",
            'label' => isset($GLOBALS["loc{$row['name']}"]) ? $GLOBALS["loc{$row['name']}"] : $row['name'],
            'type' => 'JSBUTTON',
            'style' => $printStyle,
            'listquery' => $printFunc,
            'position' => 3,
            'allow_null' => true
        ];
        if (++$rowNum > $templateFirstCol) {
            $arr['position'] = 4;
            $printButtons2[] = $arr;
        } else {
            $printButtons[] = $arr;
        }
    }

    if (count($printButtons2) > 3) {
         $printButtons2[2] = [
             'name' => 'printmenu',
             'label' => $GLOBALS['locPrintOther'],
             'type' => 'DROPDOWNMENU',
             'style' => '',
             'position' => 4,
             'options' => array_splice($printButtons2, 2)
         ];
    }

    $intRes = mysqli_query_check('SELECT ID from {prefix}base WHERE deleted=0');
    if (mysqli_num_rows($intRes) == 1)
        $defaultBase = mysqli_fetch_value($intRes);
    else
        $defaultBase = FALSE;

    $copyLinkOverride = "copy_invoice.php?func=$strFunc&amp;list=$strList&amp;id=$intInvoiceId";

    $updateInvoiceNr = null;
    if (sesWriteAccess() && !$isOffer) {
        if (!getSetting('invoice_add_number')
            || !getSetting('invoice_add_reference_number')
        ) {
            $updateInvoiceNr = '<a class="formbuttonlink" href="#" onclick="' .
            $getInvoiceNr . '">' . $GLOBALS['locGetInvoiceNr'] . '</a>';
        }
    }

    $addReminderFees = "$.getJSON('json.php?func=add_reminder_fees&amp;id=' + document.getElementById('record_id').value, function(json) { if (json.errors) { $('#errormsg').text(json.errors).show() } else { showmsg('{$GLOBALS['locReminderFeesAdded']}'); } init_rows(); }); return false;";

    $intervalOptions = [
        '0' => $GLOBALS['locInvoiceIntervalNone'],
        '2' => $GLOBALS['locInvoiceIntervalMonth'],
        '3' => $GLOBALS['locInvoiceIntervalYear']
    ];
    for ($i = 4; $i <= 8; $i++) {
        $intervalOptions[(string)$i]
            = sprintf($GLOBALS['locInvoiceIntervalMonths'], $i - 2);
    }

    $stateQuery = 'SELECT id, name FROM {prefix}invoice_state WHERE deleted=0 AND ';
    $stateQuery .= $isOffer ? 'invoice_offer=1' : 'invoice_offer!=1';
    $stateQuery .= ' ORDER BY order_no';

    $astrFormElements = [
        [
            'name' => 'base_id',
            'label' => $GLOBALS['locBiller'],
            'type' => 'LIST',
            'style' => 'long linked',
            'listquery' => 'SELECT id, name FROM {prefix}base WHERE deleted=0 ORDER BY name, id',
            'position' => 1,
            'default' => $defaultBase
        ],
        [
            'name' => 'name',
            'label' => $isOffer ? $GLOBALS['locOfferName'] : $GLOBALS['locInvName'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'company_id',
            'label' => $GLOBALS['locPayer'],
            'type' => 'SEARCHLIST',
            'style' => 'long linked',
            'listquery' => 'table=company&sort=company_name,company_id',
            'position' => 1,
            'allow_null' => true,
            'attached_elem' => $addCompanyCode,
            'elem_attributes' => $companyOnChange
        ],
        $isOffer ? [] : [
            'name' => 'reference',
            'label' => $GLOBALS['locClientsReference'],
            'type' => 'TEXT',
            'style' => 'medium',
            'position' => 2,
            'allow_null' => true
        ],
        $isOffer ? [] : [
            'name' => 'invoice_no',
            'label' => $GLOBALS['locInvoiceNumber'],
            'type' => 'INT',
            'style' => 'medium hidezerovalue',
            'position' => 1,
            'default' => null,
            'allow_null' => true
        ],
        $isOffer ? [] : [
            'name' => 'ref_number',
            'label' => $GLOBALS['locReferenceNumber'],
            'type' => 'TEXT',
            'style' => 'medium hidezerovalue',
            'position' => 2,
            'default' => null,
            'attached_elem' => $updateInvoiceNr,
            'allow_null' => true
        ],
        [
            'name' => 'invoice_date',
            'label' => $GLOBALS['locInvDate'],
            'type' => 'INTDATE',
            'style' => 'date',
            'position' => 1,
            'default' => 'DATE_NOW'
        ],
        [
            'name' => 'due_date',
            'label' => $isOffer ? $GLOBALS['locValidUntilDate']
                : $GLOBALS['locDueDate'],
            'type' => 'INTDATE',
            'style' => 'date',
            'position' => 2,
            'default' => 'DATE_NOW+' . getSetting('invoice_payment_days'),
            'attached_elem' => $updateDates
        ],
        $isOffer ? [] : [
            'name' => 'interval_type',
            'label' => $GLOBALS['locInvoiceIntervalType'],
            'type' => 'SELECT',
            'style' => 'medium',
            'position' => 1,
            'options' => $intervalOptions,
            'default' => '0',
            'allow_null' => true
        ],
        $isOffer ? [] : [
            'name' => 'next_interval_date',
            'label' => $GLOBALS['locInvoiceNextIntervalDate'],
            'type' => 'INTDATE',
            'style' => 'date',
            'position' => 2,
            'default' => '',
            'allow_null' => true
        ],
        [
            'name' => 'state_id',
            'label' => $GLOBALS['locStatus'],
            'type' => 'LIST',
            'style' => 'long translated noemptyvalue',
            'listquery' => $stateQuery,
            'position' => 1,
            'default' => $defaultState
        ],
        $isOffer ? [] : [
            'name' => 'payment_date',
            'label' => $GLOBALS['locPayDate'],
            'type' => 'INTDATE',
            'style' => 'date',
            'position' => 2,
            'allow_null' => true,
            'attached_elem' => $markPaidTodayButton,
            'elem_attributes' => 'onchange="' . $markPaidTodayEvent . '"'
        ],
        [
            'name' => 'archived',
            'label' => $GLOBALS['locArchived'],
            'type' => 'CHECK',
            'style' => 'medium',
            'position' => 1,
            'default' => 0,
            'allow_null' => true
        ],
        $isOffer ? [
            'name' => 'delivery_time',
            'label' => $GLOBALS['locDeliveryTime'],
            'type' => 'TEXT',
            'style' => 'medium hidezerovalue',
            'position' => 2,
            'default' => null,
            'allow_null' => true
        ] : [],
        [
            'name' => 'delivery_terms_id',
            'label' => $GLOBALS['locDeliveryTerms'],
            'type' => 'LIST',
            'style' => 'long',
            'listquery' => 'SELECT id, name FROM {prefix}delivery_terms WHERE deleted=0 ORDER BY order_no;',
            'position' => 1,
            'default' => null,
            'allow_null' => true
        ],
        [
            'name' => 'delivery_method_id',
            'label' => $GLOBALS['locDeliveryMethod'],
            'type' => 'LIST',
            'style' => 'medium',
            'listquery' => 'SELECT id, name FROM {prefix}delivery_method WHERE deleted=0 ORDER BY order_no;',
            'position' => 2,
            'default' => null,
            'allow_null' => true
        ],
        [
            'name' => 'info',
            'label' => $GLOBALS['locVisibleInfo'],
            'type' => 'AREA',
            'style' => 'large',
            'position' => 1,
            'attached_elem' => '<span class="select-default-text" data-type="info" data-target="info"/>',
            'allow_null' => true
        ],
        [
            'name' => 'internal_info',
            'label' => $GLOBALS['locInternalInfo'],
            'type' => 'AREA',
            'style' => 'large',
            'position' => 2,
            'allow_null' => true
        ],
        [
            'name' => 'foreword',
            'label' => $GLOBALS['locForeword'],
            'type' => 'AREA',
            'style' => 'large',
            'position' => 1,
            'attached_elem' => '<span class="select-default-text" data-type="foreword" data-target="foreword"/>',
            'allow_null' => true
        ],
        [
            'name' => 'afterword',
            'label' => $GLOBALS['locAfterword'],
            'type' => 'AREA',
            'style' => 'large',
            'position' => 2,
            'attached_elem' => '<span class="select-default-text" data-type="afterword" data-target="afterword"/>',
            'allow_null' => true
        ],

        !sesWriteAccess() || $isOffer ? [
            'name' => 'refundinvoice',
            'label' => '',
            'type' => 'FILLER',
            'position' => 1
        ] : [
            'name' => 'refundinvoice',
            'label' => $GLOBALS['locRefundInvoice'],
            'type' => 'BUTTON',
            'style' => 'redirect',
            'listquery' => "copy_invoice.php?func=$strFunc&list=$strList&id=_ID_&refund=1",
            'position' => 1,
            'default' => FALSE,
            'allow_null' => true
        ],
        !sesWriteAccess() || !$isOffer ? [
            'name' => 'copyasinvoice',
            'label' => '',
            'type' => 'FILLER',
            'position' => 1
        ] : [
            'name' => 'copyasinvoice',
            'label' => $GLOBALS['locCopyAsInvoice'],
            'type' => 'BUTTON',
            'style' => 'redirect',
            'listquery' => "copy_invoice.php?func=$strFunc&list=$strList&id=_ID_&invoice=1",
            'position' => 1,
            'default' => FALSE,
            'allow_null' => true
        ],
        $arrRefundedInvoice,
        isset($printButtons[0]) ? $printButtons[0] : [],
        isset($printButtons2[0]) ? $printButtons2[0] : [],
        !sesWriteAccess() || $isOffer ? [
            'name' => 'addreminderfees',
            'label' => '',
            'type' => 'FILLER',
            'position' => 1
        ] : [
            'name' => 'addreminderfees',
            'label' => $GLOBALS['locAddReminderFees'],
            'type' => 'JSBUTTON',
            'style' => 'redirect',
            'listquery' => $addReminderFees,
            'position' => 1,
            'default' => FALSE,
            'allow_null' => true
        ],
        $arrRefundingInvoice,
        isset($printButtons[1]) ? $printButtons[1] : [],
        isset($printButtons2[1]) ? $printButtons2[1] : [],
        !sesWriteAccess() || $isOffer ? [
            'name' => 'addpartialpayment',
            'label' => '',
            'type' => 'FILLER',
            'position' => 1
        ] : [
            'name' => 'addpartialpayment',
            'label' => $GLOBALS['locAddPartialPayment'],
            'type' => 'JSBUTTON',
            'style' => 'redirect',
            'listquery' => $addPartialPaymentCode,
            'position' => 1,
            'default' => FALSE,
            'allow_null' => true
        ],
    ];

    for ($i = 2; $i < count($printButtons); $i ++) {
        $astrFormElements[] = $printButtons[$i];
        if (isset($printButtons2[$i]))
            $astrFormElements[] = $printButtons2[$i];
    }

    $astrFormElements[] = [
        'name' => 'invoice_rows',
        'label' => $GLOBALS['locInvRows'],
        'type' => 'IFORM',
        'style' => 'xfull',
        'position' => 0,
        'allow_null' => true,
        'parent_key' => 'invoice_id'
    ];
break;

case 'invoice_row' :
case 'invoice_rows' :
$strTable = '{prefix}invoice_row';
$strJSONType = 'invoice_row';
$strParentKey = 'invoice_id';
$strOrder = 'ORDER BY {prefix}invoice_row.order_no, {prefix}invoice_row.row_date';

switch (getSetting('invoice_clear_row_values_after_add')) {
case 0 :
    break;
case 1 :
    $clearRowValuesAfterAdd = true;
    break;
case 2 :
    $onAfterRowAdded = 'MLInvoice.getSelectedProductDefaults(form_id);';
}

$astrFormElements = [
    [
        'name' => 'id',
        'label' => '',
        'type' => 'HID_INT',
        'style' => 'medium',
        'position' => 0
    ],
    [
        'name' => 'product_id',
        'label' => $GLOBALS['locProductName'],
        'type' => 'SEARCHLIST',
        'style' => 'medium',
        'listquery' => 'table=product&sort=order_no,product_code,product_name',
        'position' => 0,
        'allow_null' => true,
        'elem_attributes' => '_onChangeProduct'
    ],
    [
        'name' => 'description',
        'label' => $GLOBALS['locRowDesc'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'row_date',
        'label' => $GLOBALS['locDate'],
        'type' => 'INTDATE',
        'style' => 'date',
        'position' => 0,
        'default' => 'DATE_NOW'
    ],
    [
        'name' => 'pcs',
        'label' => $GLOBALS['locPCS'],
        'type' => 'INT',
        'style' => 'count',
        'position' => 0
    ],
    [
        'name' => 'type_id',
        'label' => $GLOBALS['locUnit'],
        'type' => 'LIST',
        'style' => 'short translated',
        'listquery' => 'SELECT id, name FROM {prefix}row_type WHERE deleted=0 ORDER BY order_no',
        'position' => 0,
        'default' => 'POST',
        'allow_null' => true
    ],
    [
        'name' => 'price',
        'label' => $GLOBALS['locPrice'],
        'type' => 'INT',
        'style' => 'currency',
        'position' => 0,
        'default' => 'POST',
        'decimals' => getSetting('unit_price_decimals')
    ],
    [
        'name' => 'discount',
        'label' => $GLOBALS['locDiscount'],
        'type' => 'INT',
        'style' => 'percent',
        'position' => 0,
        'default' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'vat',
        'label' => $GLOBALS['locVAT'],
        'type' => 'INT',
        'style' => 'percent',
        'position' => 0,
        'default' => str_replace('.', $GLOBALS['locDecimalSeparator'],
            getSetting('invoice_default_vat_percent')),
        'allow_null' => false
    ],
    [
        'name' => 'vat_included',
        'label' => $GLOBALS['locVATInc'],
        'type' => 'CHECK',
        'style' => 'xshort',
        'position' => 0,
        'default' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'order_no',
        'label' => $GLOBALS['locRowNr'],
        'type' => 'INT',
        'style' => 'tiny',
        'listquery' => 'SELECT max(order_no)+5 FROM {prefix}invoice_row WHERE deleted=0 AND invoice_id=_PARENTID_',
        'position' => 0,
        'default' => 'ADD+5',
        'allow_null' => true
    ],
    [
        'name' => 'partial_payment',
        'label' => $GLOBALS['locPartialPayment'],
        'type' => 'HID_INT',
        'style' => 'xshort',
        'position' => 0,
        'default' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'row_sum',
        'label' => $GLOBALS['locRowTotal'],
        'type' => 'ROWSUM',
        'style' => 'currency',
        'position' => 0,
        'decimals' => 2,
        'allow_null' => true
    ]
];

break;

/******************************************************************************
 SYSTEM FORMS - SYSTEEMILOMAKKEET
 ******************************************************************************/
case 'base' :
$strTable = '{prefix}base';
$strJSONType = 'base';
$addressAutocomplete = true;

$title = $GLOBALS['locBaseLogoTitle'];
$openPopJS = <<<EOF
popup_dialog('base_logo.php?func=edit&amp;id=_ID_', '$(\\'img\\').attr(\\'src\\', \\'base_logo.php?func=view&id=_ID_\\')', '$title', event, 600, 400); return false;
EOF;

$astrFormElements = [
    [
        'name' => 'name',
        'label' => $GLOBALS['locBaseName'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1
    ],
    [
        'name' => 'company_id',
        'label' => $GLOBALS['locClientVATID'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'vat_registered',
        'label' => $GLOBALS['locVATRegistered'],
        'title' => $GLOBALS['locVATRegisteredHint'],
        'type' => 'CHECK',
        'style' => 'short',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'org_unit_number',
        'label' => $GLOBALS['locOrgUnitNumber'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'payment_intermediator',
        'label' => $GLOBALS['locPaymentIntermediator'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'contact_person',
        'label' => $GLOBALS['locContactPerson'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'email',
        'label' => $GLOBALS['locEmail'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'street_address',
        'label' => $GLOBALS['locStreetAddr'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'zip_code',
        'label' => $GLOBALS['locZipCode'],
        'type' => 'TEXT',
        'style' => 'short',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'city',
        'label' => $GLOBALS['locCity'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'country',
        'label' => $GLOBALS['locCountry'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'phone',
        'label' => $GLOBALS['locPhone'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'www',
        'label' => $GLOBALS['locWWW'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'banksep1',
        'label' => $GLOBALS['locFirstBank'],
        'type' => 'LABEL'
    ],
    [
        'name' => 'bank_name',
        'label' => $GLOBALS['locBank'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1
    ],
    [
        'name' => 'bank_account',
        'label' => $GLOBALS['locAccount'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2
    ],
    [
        'name' => 'bank_iban',
        'label' => $GLOBALS['locAccountIBAN'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1
    ],
    [
        'name' => 'bank_swiftbic',
        'label' => $GLOBALS['locSWIFTBIC'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2
    ],
    [
        'name' => 'banksep2',
        'label' => $GLOBALS['locSecondBank'],
        'type' => 'LABEL'
    ],
    [
        'name' => 'bank_name2',
        'label' => $GLOBALS['locBank'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'bank_account2',
        'label' => $GLOBALS['locAccount'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'bank_iban2',
        'label' => $GLOBALS['locAccountIBAN'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'bank_swiftbic2',
        'label' => $GLOBALS['locSWIFTBIC'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'banksep3',
        'label' => $GLOBALS['locThirdBank'],
        'type' => 'LABEL'
    ],
    [
        'name' => 'bank_name3',
        'label' => $GLOBALS['locBank'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'bank_account3',
        'label' => $GLOBALS['locAccount'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'bank_iban3',
        'label' => $GLOBALS['locAccountIBAN'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'bank_swiftbic3',
        'label' => $GLOBALS['locSWIFTBIC'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'invoicesep',
        'label' => $GLOBALS['locBaseInvoiceTexts'],
        'type' => 'LABEL'
    ],
    [
        'name' => 'invoice_default_info',
        'label' => $GLOBALS['locInvoiceDefaultInfo'],
        'type' => 'AREA',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'invoice_default_foreword',
        'label' => $GLOBALS['locInvoiceDefaultForeword'],
        'type' => 'AREA',
        'style' => 'large',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'invoice_default_afterword',
        'label' => $GLOBALS['locInvoiceDefaultAfterword'],
        'type' => 'AREA',
        'style' => 'large',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'offer_default_foreword',
        'label' => $GLOBALS['locOfferDefaultForeword'],
        'type' => 'AREA',
        'style' => 'large',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'offer_default_afterword',
        'label' => $GLOBALS['locOfferDefaultAfterword'],
        'type' => 'AREA',
        'style' => 'large',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'terms_of_payment',
        'label' => $GLOBALS['locSettingInvoiceTermsOfPayment'],
        'type' => 'TEXT',
        'style' => 'large',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'period_for_complaints',
        'label' => $GLOBALS['locSettingInvoicePeriodForComplaints'],
        'type' => 'TEXT',
        'style' => 'large',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'emailsep',
        'label' => $GLOBALS['locBaseEmailTitle'],
        'type' => 'LABEL'
    ],
    [
        'name' => 'invoice_email_from',
        'label' => $GLOBALS['locBaseEmailFrom'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'invoice_email_bcc',
        'label' => $GLOBALS['locBaseEmailBCC'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'invoice_email_subject',
        'label' => $GLOBALS['locBaseInvoiceEmailSubject'],
        'type' => 'TEXT',
        'style' => 'long',
        'position' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'invoice_email_body',
        'label' => $GLOBALS['locBaseInvoiceEmailBody'],
        'type' => 'AREA',
        'style' => 'email email_body',
        'position' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'receipt_email_subject',
        'label' => $GLOBALS['locBaseReceiptEmailSubject'],
        'type' => 'TEXT',
        'style' => 'long',
        'position' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'receipt_email_body',
        'label' => $GLOBALS['locBaseReceiptEmailBody'],
        'type' => 'AREA',
        'style' => 'email email_body',
        'position' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'order_confirmation_email_subject',
        'label' => $GLOBALS['locBaseOrderConfirmationEmailSubject'],
        'type' => 'TEXT',
        'style' => 'long',
        'position' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'order_confirmation_email_body',
        'label' => $GLOBALS['locBaseOrderConfirmationEmailBody'],
        'type' => 'AREA',
        'style' => 'email email_body',
        'position' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'offer_email_subject',
        'label' => $GLOBALS['locBaseOfferEmailSubject'],
        'type' => 'TEXT',
        'style' => 'long',
        'position' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'offer_email_body',
        'label' => $GLOBALS['locBaseOfferEmailBody'],
        'type' => 'AREA',
        'style' => 'email email_body',
        'position' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'logosep',
        'label' => $GLOBALS['locBaseLogoTitle'],
        'type' => 'LABEL'
    ],
    [
        'name' => 'logo',
        'label' => '',
        'type' => 'IMAGE',
        'style' => 'image',
        'listquery' => 'base_logo.php?func=view&amp;id=_ID_',
        'position' => 0,
        'allow_null' => true
    ],
    [
        'name' => 'edit_logo',
        'label' => $GLOBALS['locBaseChangeImage'],
        'type' => 'JSBUTTON',
        'style' => 'medium',
        'listquery' => $openPopJS,
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'logo_left',
        'label' => $GLOBALS['locBaseLogoLeft'],
        'type' => 'INT',
        'style' => 'measurement',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'logo_top',
        'label' => $GLOBALS['locBaseLogoTop'],
        'type' => 'INT',
        'style' => 'measurement',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'logo_width',
        'label' => $GLOBALS['locBaseLogoWidth'],
        'type' => 'INT',
        'style' => 'measurement',
        'position' => 1,
        'allow_null' => true
    ]
];
break;

case 'invoice_state' :
$levelsAllowed = [
    ROLE_ADMIN
];
$strTable = '{prefix}invoice_state';
$strJSONType = 'invoice_state';

$intId = isset($id) ? $id : getRequest('id', FALSE);
$readOnly = ($intId && $intId <= 8);
$astrFormElements = [
    [
        'name' => 'name',
        'label' => $GLOBALS['locStatus'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'read_only' => $readOnly
    ],
    [
        'name' => 'order_no',
        'label' => $GLOBALS['locOrderNr'],
        'type' => 'INT',
        'style' => 'short',
        'position' => 2,
        'read_only' => $readOnly
    ],
    [
        'name' => 'invoice_open',
        'label' => $GLOBALS['locInvoiceStatusOpen'],
        'type' => 'CHECK',
        'style' => 'short',
        'position' => 1
    ],
    [
        'name' => 'invoice_unpaid',
        'label' => $GLOBALS['locInvoiceStatusUnpaid'],
        'type' => 'CHECK',
        'style' => 'short',
        'position' => 2
    ],
    [
        'name' => 'invoice_offer',
        'label' => $GLOBALS['locInvoiceStatusOffer'],
        'type' => 'CHECK',
        'style' => 'short',
        'position' => 1
    ]

];
break;

case 'row_type' :
$levelsAllowed = [
    ROLE_ADMIN
];
$strTable = '{prefix}row_type';
$strJSONType = 'row_type';

$astrFormElements = [
    [
        'name' => 'name',
        'label' => $GLOBALS['locRowType'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1
    ],
    [
        'name' => 'order_no',
        'label' => $GLOBALS['locOrderNr'],
        'type' => 'INT',
        'style' => 'short',
        'position' => 2
    ]
];
break;

case 'session_type' :
$levelsAllowed = [
    ROLE_ADMIN
];
$strTable = '{prefix}session_type';
$strJSONType = 'session_type';

$intId = getRequest('id', FALSE);
if ($intId && $intId <= 4) {
    $readOnlyForm = true;
}
$astrFormElements = [
    [
        'name' => 'name',
        'label' => $GLOBALS['locSessionType'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1
    ],
    [
        'name' => 'order_no',
        'label' => $GLOBALS['locOrderNr'],
        'type' => 'INT',
        'style' => 'short',
        'position' => 2
    ],
    [
        'name' => 'access_level',
        'label' => $GLOBALS['locAccessLevel'],
        'type' => 'INT',
        'style' => 'short',
        'position' => 1,
        'default' => 1
    ]
];
break;

case 'delivery_terms' :
$levelsAllowed = [
    ROLE_ADMIN
];
$strTable = '{prefix}delivery_terms';
$strJSONType = 'delivery_terms';

$astrFormElements = [
    [
        'name' => 'name',
        'label' => $GLOBALS['locDeliveryTerms'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1
    ],
    [
        'name' => 'order_no',
        'label' => $GLOBALS['locOrderNr'],
        'type' => 'INT',
        'style' => 'short',
        'position' => 2
    ]
];
break;

case 'delivery_method' :
$levelsAllowed = [
    ROLE_ADMIN
];
$strTable = '{prefix}delivery_method';
$strJSONType = 'delivery_method';

$astrFormElements = [
    [
        'name' => 'name',
        'label' => $GLOBALS['locDeliveryMethod'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1
    ],
    [
        'name' => 'order_no',
        'label' => $GLOBALS['locOrderNr'],
        'type' => 'INT',
        'style' => 'short',
        'position' => 2
    ]
];
break;

case 'default_value' :
$levelsAllowed = [
    ROLE_ADMIN
];
$strTable = '{prefix}default_value';
$strJSONType = 'default_value';

$astrFormElements = [
    [
        'name' => 'type',
        'label' => $GLOBALS['locDefaultValueType'],
        'type' => 'LIST',
        'style' => 'medium',
        'listquery' => [
            'info' => $GLOBALS['locInfo'],
            'foreword' => $GLOBALS['locForeword'],
            'afterword' => $GLOBALS['locAfterword']
        ],
        'position' => 1
    ],
    [
        'name' => 'name',
        'label' => $GLOBALS['locName'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1
    ],
    [
        'name' => 'order_no',
        'label' => $GLOBALS['locOrderNr'],
        'type' => 'INT',
        'style' => 'short',
        'position' => 2
    ],
    [
        'name' => 'content',
        'label' => $GLOBALS['locContent'],
        'type' => 'AREA',
        'style' => 'xlarge',
        'position' => 0
    ]
];
break;

case 'user' :
$levelsAllowed = [
    ROLE_ADMIN
];
$strTable = '{prefix}users';
$strJSONType = 'user';
$astrFormElements = [
    [
        'name' => 'name',
        'label' => $GLOBALS['locUserName'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1
    ],
    [
        'name' => 'login',
        'label' => $GLOBALS['locLoginName'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'unique' => true
    ],
    [
        'name' => 'passwd',
        'label' => $GLOBALS['locPassword'],
        'type' => 'PASSWD',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'type_id',
        'label' => $GLOBALS['locType'],
        'type' => 'LIST',
        'style' => 'medium translated',
        'listquery' => 'SELECT id, name FROM {prefix}session_type WHERE deleted=0 ORDER BY order_no',
        'position' => 0
    ]
];
break;

case 'print_template' :
$strTable = '{prefix}print_template';
$strJSONType = 'print_template';

$elem_attributes = '';
$astrFormElements = [
    [
        'name' => 'type',
        'label' => $GLOBALS['locPrintTemplateType'],
        'type' => 'LIST',
        'style' => 'medium noemptyvalue',
        'listquery' => [
            'invoice' => $GLOBALS['locPrintTemplateTypeInvoice'],
            'offer' => $GLOBALS['locPrintTemplateTypeOffer']
        ],
        'position' => 1
    ],
    [
        'name' => 'order_no',
        'label' => $GLOBALS['locOrderNr'],
        'type' => 'INT',
        'style' => 'short',
        'position' => 2
    ],
    [
        'name' => 'name',
        'label' => $GLOBALS['locPrintTemplateName'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1
    ],
    [
        'name' => 'filename',
        'label' => $GLOBALS['locPrintTemplateFileName'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'parameters',
        'label' => $GLOBALS['locPrintTemplateParameters'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'output_filename',
        'label' => $GLOBALS['locPrintTemplateOutputFileName'],
        'type' => 'TEXT',
        'style' => 'medium',
        'position' => 2,
        'allow_null' => true
    ],
    [
        'name' => 'new_window',
        'label' => $GLOBALS['locPrintTemplateOpenInNewWindow'],
        'type' => 'CHECK',
        'style' => 'medium',
        'position' => 1,
        'allow_null' => true
    ],
    [
        'name' => 'inactive',
        'label' => $GLOBALS['locPrintTemplateInactive'],
        'type' => 'CHECK',
        'style' => 'medium',
        'position' => 2,
        'default' => 0,
        'allow_null' => true
    ]
];
break;
}

// Clean up the array
$akeys = [
'name',
'type',
'position',
'style',
'label',
'parent_key',
'listquery',
'allow_null',
'elem_attributes'
];
foreach ($astrFormElements as &$element) {
foreach ($akeys as $key) {
    if (!isset($element[$key])) {
        $element[$key] = false;
    }
}
}
