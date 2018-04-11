    <div class="row">  

 <div class="panel">
 {$version.version_status}
{if $version.version_status == '1'}

<div class="alert alert-danger">
<img src="../modules/mokasanalpos/img/kahvedigital.png" style="float:left; margin-right:15px; max-width:150px;">


Yeni bir versiyon mevcut güncellemek için <a href='{$link}&updated_moka={$version.new_version_id}'>tıklayınız</a>.


</div>

{else}
<div class="alert alert-info">
<img src="../modules/mokasanalpos/img/kahvedigital.png" style="float:left; margin-right:15px; max-width:150px;">
<p><strong>{l s="KahveDigital olarak modüllerimizi sürekli güncelleyerek kolay ve sorunsuz e-ticaret yapmanıza olanak sağlıyoruz." mod='mokasanalpos'}</strong></p>
<p>{l s="En güncel modül versiyonu kullanmaktasınız bu sayfayı ziyeret ederek güncellemelerinizi kontrol edebilirsiniz." mod='mokasanalpos'}</p>

<br/>

<p><strong>{l s="Note:  Modül geliştirme ile önerilerinizi ve isteklerinizi hello@kahvedigital.com üzerinden bizlere ulaştırabilirsiniz " mod='mokasanalpos'}</strong></p>
</div>

{/if}


</div>

</div>