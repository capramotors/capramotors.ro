{if $tbiro_status == 'ok'}
<p>{l s='Your order on is complete.' d='Modules.Tbiropayment.Front'}
        <br /><br />- {l s='Amount' d='Modules.Tbiropayment.Front'} <span class="price"><strong>{$tbiro_total_to_pay}</strong></span>
    </p>
{else}
    <p class="warning">
        {l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' d='Modules.Tbiropayment.Front'} 
        <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team' d='Modules.Tbiropayment.Front'}</a>.
    </p>
{/if}
<style>
#tbiroloaderpanel {
    display: block;
    position: fixed;
    top:calc(100% / 2);
    left:calc(100% / 2 - 200px);
    background: white;
    z-index:999;
    border: 2px solid #f3f3f3;
    width: 400px;
    height: 90px;
}
#tbiroloader {
    position: absolute; 
    top:10px;
    left:10px;
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #f07524;
    width: 40px;
    height: 40px;
    -webkit-animation: spin 2s linear infinite; /* Safari */
    animation: spin 2s linear infinite;
}
#tbiroloadertext {
    position: absolute; 
    top: 0px;
    left: 90px;
    padding:10px;
    width: 100% - 90px;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    color: black;;
}
#tbiroloaderimg {
    position: absolute; 
    top:45px;
    width: 100%;
    text-align: center;
}
/* Safari */
@-webkit-keyframes spin {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(360deg); }
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
<div id="tbiroloaderpanel">
<div id="tbiroloader"></div>
<div id="tbiroloadertext">{$tbi_pause_txt}</div>
<div id="tbiroloaderimg"><img src="{$tbirologo}" alt="TBI" /></div>
</div>
{if $type eq 'IRIS'}
    {if $tbiro_url != "" }
    <script>
    window.location.href = "{$tbiro_url}";
    </script>
    {/if}
{else}
<script>
function createCORSRequest(method, url) {
    var xhr = new XMLHttpRequest();
    if ("withCredentials" in xhr) {
        xhr.open(method, url, true);
    } else if (typeof XDomainRequest != "undefined") {
        xhr = new XDomainRequest();
        xhr.open(method, url);
    } else {
        xhr = null;
    }
    return xhr;
}
var data = new FormData();
data.append('order_data', '{$tbiro_output64}');
data.append('providerCode', 'avast');
var xmlhttpro = createCORSRequest('POST', '{$tbiro_envurl}');
    
xmlhttpro.onreadystatechange = function() {
    if (this.readyState == 2) {
        var headers = this.responseURL;
        window.location.href = headers;
    }
};
    
xmlhttpro.send(data);
</script>
{/if}