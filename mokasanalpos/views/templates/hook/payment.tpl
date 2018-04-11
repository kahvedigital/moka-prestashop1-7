<link rel="stylesheet" type="text/css" href="{$module_dir|escape:'htmlall':'UTF-8'}views/css/moka.css">
    <div class= "row"> 
        <div class="col-xs-12">

                {if (isset($error)) }
                <div class="paiement_block">
                    <p class="alert alert-warning">{$error}</p>
                </div>
                {else}
   <p class="alert alert-warning" id="terms-error">{$error_terms}</p>
                <div id="moka-form" class="mokaform" > 

                    <div class="tum">
                        <h3 class="odemeform-baslik">Ödeme Formu</h3>
                        <div class="hepsi">
						    <div class="card-wrapper"></div>
                            <div class="demo-container">
                                <div class="info-window cvc " ><div class="arrow-info"></div><div class="cvc-info"><img src="{$module_dir|escape:'htmlall':'UTF-8'}img/cvc-help.png"></div></div>

                                <div class="form-group active moka">
                                    <form action="{$url}" method="POST" id="mokapostform">


                                        <div class="mokaname mokafull">
                                            <input class="c-card card-name" placeholder="Kart İsim Soyisim" type="text" required    oninvalid="this.setCustomValidity('Kart sahibinin adını yazınız.')"  oninput="setCustomValidity('')" name="name" >
                                        </div>


                                        <div class="mokacard mokaorta">
                                            <i class="mokacardicon"></i>
                                            <input id="mokacardnumber" class="c-card cardnumber" placeholder="Kart Numarası" required   oninvalid="this.setCustomValidity('Kartın üzerindeki 16 haneli numarayı giriniz.')" oninput="setCustomValidity('')" type="tel" name="number" >
                                        </div>


                                        <div class="mokaleft mokaexpry">
                                            <input class="c-date c-card"  placeholder="AA/YY" type="tel" maxlength="7" required  oninvalid="this.setCustomValidity('Kartın son kullanma tarihini giriniz')" oninput="setCustomValidity('')" name="expiry" >
                                        </div>

                                        <div class="mokaright mokacvc">
                                            <input class="card-cvc c-card" placeholder="CVC" required  type="number"  oninvalid="this.setCustomValidity('Kartın arkasındaki 3 ya da 4 basamaklı sayıyı giriniz')" oninput="setCustomValidity('')" name="cvc" >
                                                <div class="moka-i-icon"><img src="{$module_dir|escape:'htmlall':'UTF-8'}img/icons/info.png" width="14px"> </div>
                                        </div>

                                </div>

                            </div>

                            <div class="tekcekim-container ">

                                <div class="tekcekim">

                                    <li class="taksit-li " for="s-option" >
                                        <input type="radio" id="s-option"  name="mokatotal"  value="{Tools::displayPrice($total)}" checked class="option-input taksitradio radio " >
                                            <label for="s-option">Tek Çekim</label>
                                            <div class="taksit-fiyat"> {Tools::displayPrice($total)} </div>
                                            <div class="check"><div class="inside"></div></div>
                                    </li>
					
                                    <div class="taksit-secenek">
										{if $taksit == "taksit" }
                                        <h3 class="taksit-secenekleri">Taksit Seçenekleri</h3>
									
                                        <div class="logolar-moka">

                                            {foreach from=$rates key=bank item=rate}
                                            {if $rates.$bank.active == 1 }


                                            <div class="moka-banka-logo {$bank}-logo"><img src="{$module_dir|escape:'htmlall':'UTF-8'}img/{$bank}.svg"	></img></div>

                                            {/if}
                                            {/foreach}

                                        </div>
										  {/if}
                                    </div>
                                </div>  
                            </div>  
	{if $taksit == "taksit" }
                            <div class="taksit-container ">
                                {foreach from=$rates key=bank item=rate}
                                {if $rates.$bank.active == 1 }





                                <div class="{$bank}">
                                    <div class="taksit-title "><img src="{$module_dir|escape:'htmlall':'UTF-8'}img/{$bank}.svg"></div>

                                    {for $ins=1 to 12}
                                    {foreach from=$rates key=banks item=rate}
                                    {if $bank == $banks}
                                    {if $ins<=$max_taksit}
                                    {if $rates.$bank.active == 1 }

                                    <li class="taksit-li mokaorta">
                                        <input type="radio" id="s-option_{$bank}_{$ins}" name="mokatotal[{$bank}][{$ins}]" value="{Tools::displayPrice($rates.$bank.installments.$ins.total)}" class="option-input  taksitradio radio">
                                            <label for="s-option2">{$ins} Taksit</label>
                                            <div class="taksit-fiyat"> {Tools::displayPrice($rates.$bank.installments.$ins.total)} / {Tools::displayPrice($rates.$bank.installments.$ins.monthly)} </div>
                                            <div class="check"><div class="inside"></div></div>
                                    </li>



                                    {/if}		

                                    {/if}
                                    {/if}
                                    {/foreach}
                                    </tr>
                                    {/for}




                                </div>	

                                {/if}

                                {/foreach}

                            </div>
    {/if}	
                            <button type="submit" class="mokaode" href="javascript:;" style=""><span class="mokaOdemeTutar">{Tools::displayPrice($total)}</span><span class="mokaOdemeText"> ÖDE</span></button>
                            </form>

                        </div>
                    

                    </div>

                </div>  

                {/if}
                {if (isset($currency_error) && $currency_error != '')}
                <p class="alert alert-warning">{$currency_error}</p> 
                {/if}
            </div>
        </div>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		
		{literal}
<script>
    $(document).ready(function () {
	$( "#checkout-payment-step > h1" ).click(function() {
	$("#js-delivery").submit();
	$('#moka-form').hide();
	$('#checkout-payment-step > div').hide();
	
});
	
	
	
        $('#moka-form').hide();
        $("input[name='payment-option']").click(function () {
            $("button[class='btn btn-primary center-block']").show();
        });
        $("input[data-module-name='mokasanalpos']").click(function () {
            $("button[class='btn btn-primary center-block']").hide();
        });
        $("input[id='conditions_to_approve[terms-and-conditions]']").change(function () {
            if (this.checked) {
                $('#moka-form').show();
                $('#terms-error').hide();
            } else {
                $('#moka-form').hide();
                $('#terms-error').show();
            }
        });
    });
</script>
{/literal}
  <script src="{$module_dir|escape:'htmlall':'UTF-8'}views/js/card.js"></script>
        <script>
            var theme = "{$module_dir|escape:'htmlall':'UTF-8'}";
   var taksit = "{$taksit|escape:'htmlall':'UTF-8'}";
        </script>
        {literal}

        <script type="text/javascript">
            new Card({
                form: document.querySelector('.hepsi'),
                container: '.card-wrapper'
            });

            $(document).ready(function () {
                $('input[type=radio][name=mokatotal]').change(function () {

                    $('.mokaOdemeTutar').text(this.value);




                });
            });

if(taksit=='taksit'){	
            $(document).ready(function () {


                cardshow(0);

                $(".maximum-logo").click(function () {
                    cardshow(0);
                    $(".taksit-container").show();
                    $(".maximum").show();
                    $("#s-option_maximum_1").prop('checked', true);
                });

                $(".cardfinans-logo").click(function () {
                    cardshow(0);
                    $(".taksit-container").show();
                    $(".cardfinans").show();
                    $("#s-option_cardfinans_1").prop('checked', true);
                });
                $(".axess-logo").click(function () {
                    cardshow(0);
                    $(".taksit-container").show();
                    $(".axess").show();
                    $("#s-option_axess_1").prop('checked', true);
                });
                $(".bonus-logo").click(function () {
                    cardshow(0);
                    $(".taksit-container").show();
                    $(".bonus").show();
                    $("#s-option_bonus_1").prop('checked', true);
                });
                $(".world-logo").click(function () {
                    cardshow(0);
                    $(".taksit-container").show();
                    $(".world").show();
                    $("#s-option_world_1").prop('checked', true);
                });


                $(".taksit-li").click(function () {
                    $(".taksit-li").find('input[type="radio"]').removeAttr('checked');
                    $(this).find('input[type="radio"]').prop('checked', true);
                    var price = $(this).find('input[type="radio"]').val();
                    $('.mokaOdemeTutar').text(price);
                });


                function cardshow(bankcode) {

                    if (bankcode == '0') {
                        $(".taksit-container").hide();
                        $(".taksit-container").children().hide();
                    } else if ((bankcode == 62) || (bankcode == 59) || (bankcode == 32) || (bankcode == 99) || (bankcode == 124) || (bankcode == 134) || (bankcode == 206)) {
                        $(".taksit-container").hide();
                        $(".taksit-container").children().hide();
                        $(".taksit-container").show();
                        $('.bonus').show();
                        $("#s-option_bonus_1").prop('checked', true);

                    } else if ((bankcode == 46) || (bankcode == 92)) {


                        $(".taksit-container").hide();
                        $(".taksit-container").children().hide();
                        $(".taksit-container").show();
                        $('.axess').show();

                        $("#s-option_axess_1").prop('checked', true);
                    } else if ((bankcode == 64) || (bankcode == 10)) {


                        $(".taksit-container").hide();
                        $(".taksit-container").children().hide();
                        $(".taksit-container").show();
                        $('.maximum').show();


                        $("#s-option_maximum_1").prop('checked', true);
                    } else if ((bankcode == 15) || (bankcode == 67) || (bankcode == 135) || (bankcode == 203)) {


                        $(".taksit-container").hide();
                        $(".taksit-container").children().hide();
                        $(".taksit-container").show();
                        $('.world').show();

                        $("#s-option_world_1").prop('checked', true);

                    } else if (bankcode == 111) {


                        $(".taksit-container").hide();
                        $(".taksit-container").children().hide();
                        $(".taksit-container").show();
                        $('.cardfinans').show();


                    }




                }




                $.ajaxSetup({cache: false});
                $('#mokacardnumber').keyup(function () {
                    var searchField = $('#mokacardnumber').val();
                    searchField = searchField.replace(/\s/g, '');
                    if (searchField.length < 6) {
                        cardshow(0);
                        return
                    }
                    ;
                    if (searchField.length > 6)
                        return;

                    $.getJSON('' + theme + 'bins.json', function (data) {

                        $.each(data, function (key, value) {

                            if (value.bin_number == searchField)
                            {

                                cardshow(value.bank_code);
                            }
                        });
                    });
                });

            });
			}
			
		

            $(".moka-i-icon img").hover(function () {
                $(".info-window").toggleClass("info-window-active");
            });




            $('.c-card').bind('keypress keyup keydown focus', function (e) {
                var ErrorInput = false;


                if ($("input.card-name").hasClass("jp-card-invalid")) {
                    ErrorInput = true;
                    $("input.card-name").addClass("border");
                }
                if ($("input.cardnumber").hasClass("jp-card-invalid")) {
                    ErrorInput = true;
                    $("input.cardnumber").addClass("border");
                }
                if ($("input.c-date").hasClass("jp-card-invalid")) {
                    ErrorInput = true;
                    $("input.c-date").addClass("border");
                }
                if ($("input.card-cvc").hasClass("jp-card-invalid")) {
                    ErrorInput = true;
                    $("input.card-cvc").addClass("border");
                }
                if (ErrorInput === true) {
                    $('.mokaode').attr("disabled", true);

                    $(".mokaode").css("opacity", "0.5");



                } else {

                    $("input.card-name").removeClass("border");
                    $("input.cardnumber").removeClass("border");
                    $("input.c-date").removeClass("border");
                    $("input.card-cvc").removeClass("border");
                    $('.mokaode').attr("disabled", false);

                    $(".mokaode").css("opacity", "1");

                }


            });



        </script>
        {/literal}