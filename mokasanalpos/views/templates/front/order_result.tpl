{extends file='page.tpl'}
{block name='page_content'}
<p>
   {if !empty({$error})}
<div class="alert alert-danger">
			HATA !!!!  Ödeme isleminiz tamamlanamadi. <br>
			<p>Banka Cevabi :   {$error}</p>
		</div>      

	
   {else}
       {if $locale == 'tr'}
	 
       <div class="alert alert-success">
				<p><span>Basarili !</span> Odeme Isleminiz Basariyla Tamamlandi. Teşekkürler</p>
				<p>  {$success}</p>
			
			</div>
		

        {else}
		
		    <div class="alert alert-success">
				<p><span>Successfully !</span>  Received your order and your payment Thank you.</p>
				<p>  {$success}</p>
			
			</div>
     
        
        {/if}
    {/if}
</p>
{/block}
