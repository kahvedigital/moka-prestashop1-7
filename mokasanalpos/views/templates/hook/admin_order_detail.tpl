
<div class="panel panel-highlighted">
    <div class="panel-heading">
        <i class="icon-credit-card"></i>Moka Ödeme Detayı <span class="badge">{$record->Data->PaymentDetail->DealerPaymentId|escape:'html':'UTF-8'}</span>
    </div>
    <table class="display table">
        <tr>
            <td>Ödenen Tutar </td><td> {displayPrice price=$record->Data->PaymentDetail->Amount}</td>
        </tr>
        <tr>
            <td>Ödeme Komisyon </td><td> {displayPrice price=$record->Data->PaymentDetail->DealerCommissionAmount}</td>
        </tr>
        <tr>
            <td>Moka Referans Numarası </td><td> {$record->Data->PaymentDetail->DealerPaymentId|escape:'html':'UTF-8'}</td>
        </tr>
        <tr>
            <td>Kredi Kartı</td><td> {$record->Data->PaymentDetail->CardHolderFullName|escape:'html':'UTF-8'} {$record->Data->PaymentDetail->CardNumberFirstSix|escape:'html':'UTF-8'}XXXXXX{$record->Data->PaymentDetail->CardNumberLastFour|escape:'html':'UTF-8'}</td>
        </tr>
        <tr>
            <td>Taksit  Sayısı</td><td> {$record->Data->PaymentDetail->InstallmentNumber}</td>
        </tr>
        <tr>
            <td>Result Code </td><td> {$record->Data->ResultCode}</td>
        </tr>
    </table>
</div>

<hr/>