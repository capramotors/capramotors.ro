{if $tbiro_status_local eq 1}
{if $tbiro_status eq 'Yes'}
{if $tbiro_container_status eq 'Yes'}
<div class="tbiro_float" onclick="tbiroChangeContainer();">
    <img src="{$tbiro_logo}" class="tbiro-my-float">
</div>
<div class="tbiro-label-container">
    <i class="fa fa-play fa-rotate-180 tbiro-label-arrow"></i>
    <div class="tbiro-label-text">
        <div style="padding-bottom:5px;"></div>
        <img src="{$tbiro_picture}">
        <div style="font-size:16px;padding-top:3px;">{$tbiro_container_txt1}</div>
        <p>{$tbiro_container_txt2}</p>
        <div class="tbiro-label-text-a">{l s='CREDIT ONLINE INFORMATION WITH TBI BANK!' d='Modules.Tbiropayment.Front'}</div>
    </div>
</div>
<script type="application/javascript">
    function tbiroChangeContainer(){
        var tbiro_label_container = document.getElementsByClassName("tbiro-label-container")[0];
        if (tbiro_label_container.style.visibility == 'visible'){
            tbiro_label_container.style.visibility = 'hidden';
            tbiro_label_container.style.opacity = 0;
            tbiro_label_container.style.transition = 'visibility 0s, opacity 0.5s ease';
        }else{
            tbiro_label_container.style.visibility = 'visible';
            tbiro_label_container.style.opacity = 1;
        }
    }
</script>
{/if}
{/if}
{/if}