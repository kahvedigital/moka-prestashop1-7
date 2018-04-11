{extends file='page.tpl'}
{block name='page_content'}
<p>
   {if !empty({$error})}
        {$error}

  {else}
   
   
   
   
   <div class="alert alert-success">
			<p>KART BILGILERINIZ DOGRULANDI !!!
			<br><br>
				Simdi kart güvenligini dogrulamak için bankanin sayfasina yönlendirileceksiniz. 
			<br>
				Burada kart sahibinin bankada kayitli cep telefonuna gelecek SMS\'i girdikten sonra islemleriniz tamamlanacaktir.
			</p>
			<p>
				<a href="{$data}" class="btn btn-primary">Yönlendirme çalismaz ise buraya tiklayin...</a>				
			</p>
		</div>
 

    {/if}

{/block}