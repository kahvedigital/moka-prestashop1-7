{if !$err}
{/if}


<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="active"><a href="#mokasettings" role="tab" data-toggle="tab">Genel Ayarlar</a></li>
	<li><a href="#taksit" role="tab" data-toggle="tab">{l s='Taksit' }</a></li>
	<li><a href="#moka" role="tab" data-toggle="tab">Moka</a></li>
	<li><a href="#help" role="tab" data-toggle="tab">{l s='Yardım' }</a></li>
	<li><a href="#update" role="tab" data-toggle="tab">{l s='Update' }</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane active" id="mokasettings">{include file='./settingsform.tpl'}</div>
	<div class="tab-pane" id="taksit">{include file='./taksit.tpl'}</div>
	<div class="tab-pane" id="moka">{include file='./moka.tpl'}</div>
	<div class="tab-pane" id="help">{include file='./help.tpl'}</div>
	<div class="tab-pane" id="update">{include file='./update.tpl'}</div>
</div>
        
        
<div class="panel">
	<div class="col-sm-1 text-center">
		<a href="http://kahvedigital.com" ><img src="{$module_dir|escape:'html':'UTF-8'}/img/kahvedigital.png" class="col-sm-12 text-center" id="payment-logo" /></a>
	</div>
	<div class="col-sm-7 text-center">
	<p class="text-muted">


	</p>
	</div>
	<div class="col-sm-4 text-center">
				<a href="http://kahvedigital.com"><img src="{$module_dir|escape:'html':'UTF-8'}/img/icons/web.png" width="32px" /></a>
				<a href="https://www.facebook.com/kahvedigital"><img src="{$module_dir|escape:'html':'UTF-8'}/img/icons/facebook.png" width="32px" /></a>
            <a href="https://twitter.com/kahvedigital"><img src="{$module_dir|escape:'html':'UTF-8'}/img/icons/twitter.png" width="32px" /></a>
            <a href="https://www.youtube.com/user/kahvedigital"><img src="{$module_dir|escape:'html':'UTF-8'}/img/icons/youtube.png" width="32px" /></a>
            <a href="https://www.linkedin.com/company/kahve-digital"><img src="{$module_dir|escape:'html':'UTF-8'}/img/icons/linkedin.png" width="32px" /></a>
            <a href="https://www.instagram.com/kahvedigital/"><img src="{$module_dir|escape:'html':'UTF-8'}/img/icons/instagram.png" width="32px" /></a>         
            <a href="https://github.com/kahvedigital/"><img src="{$module_dir|escape:'html':'UTF-8'}/img/icons/github.png" width="32px" /></a>
	</div>
	<hr/>
</div>

