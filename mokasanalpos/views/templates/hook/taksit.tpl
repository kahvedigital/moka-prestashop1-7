
<div class="panel">
	<div class="row moka-header">
            <h2>{l s='Taksit Vade Oran Ayarları'}</h2>
            <p> {l s='Bu oranlar, müşteriniz tarafından seçilen taksitlerin toplam tutarını hesaplamak için kullanılacaktır.'}
	</div>

	<hr />
	
	<div class="moka-content">
		<div class="row">
			<div class="col-md-12">
                            <form action="" method="post">
                            {$kahvedigital_taksit_form}
                            <input type="hidden" name="kahvedigital_moka_taksit_update" value="1"/>
                            <button type="submit" class="btn btn-large btn-success">{l s='Save'}</button>
                            </form>			
			</div>		
		</div>
		<hr />
	</div>
</div>
