{if $tbiro_status_local eq 1}
{if $tbiro_btnvisible eq 'Yes'}
{if $tbiro_zaglavie ne '' or $tbiro_opisanie ne '' or $tbiro_product ne ''}
    <span style="font-size:22px;font-weight:bold;">{$tbiro_zaglavie}</span> <span style="font-size:18px;">{$tbiro_opisanie}</span> {$tbiro_product}
{/if}
<br />
{if $tbiro_custom_button_status eq 'Yes'}
    {if $tbiro_vnoska eq 'Yes'}
    <table border="0">
        <tr>
            <td style="padding-right:5px;padding-bottom:5px;">
                {if $tbiro_backurl eq ''}
                <img id="btn_tbiro" style="padding-bottom: 5px;" src="{$tbiro_liveurl}/calculators/assets/img/custom_buttons/{$unicid}.png" title="Credit module tbi bank {$tbiro_mod_version}" alt="Credit module tbi bank {$tbiro_mod_version}" onmouseover="this.src='{$tbiro_liveurl}/calculators/assets/img/custom_buttons/{$unicid}_hover.png'" onmouseout="this.src='{$tbiro_liveurl}/calculators/assets/img/custom_buttons/{$unicid}.png'">
                {else}
                <a href="{$tbiro_backurl}" target="_blank" title="Go to tbi bank page"><img id="btn_tbiro" style="padding-bottom: 5px;cursor:pointer;" src="{$tbiro_liveurl}/calculators/assets/img/custom_buttons/{$unicid}.png" title="Credit module tbi bank {$tbiro_mod_version}" alt="Credit module tbi bank {$tbiro_mod_version}" onmouseover="this.src='{$tbiro_liveurl}/calculators/assets/img/custom_buttons/{$unicid}_hover.png'" onmouseout="this.src='{$tbiro_liveurl}/calculators/assets/img/custom_buttons/{$unicid}.png'"></a>
                {/if}
            </td>
        </tr>
        <tr>
            <td style="vertical-align:bottom;">
                <p style="color:{$tbi_btn_color}font-size:16pt;font-weight:bold;"> {$tbiro_mesecna} {$currency['sign']} x {$tbi_months} {l s='months' d='Modules.Tbiropayment.Front'}</p>
            </td>
        </tr>
    </table>
    {else}
        <a href="{$tbiro_backurl}" target="_blank" title="Go to tbi bank page"><img id="btn_tbiro" style="padding-bottom: 5px;cursor:pointer;" src="{$tbiro_liveurl}/calculators/assets/img/custom_buttons/{$unicid}.png" title="Credit module tbi bank {$tbiro_mod_version}" alt="Credit module tbi bank {$tbiro_mod_version}" onmouseover="this.src='{$tbiro_liveurl}/calculators/assets/img/custom_buttons/{$unicid}_hover.png'" onmouseout="this.src='{$tbiro_liveurl}/calculators/assets/img/custom_buttons/{$unicid}.png'"></a>
    {/if}
{else}
    {if $tbiro_vnoska eq 'Yes'}
    <table border="0">
        <tr>
            <td style="padding-right:5px;padding-bottom:5px;">
                {if $tbiro_backurl eq ''}
                <img id="btn_tbiro" style="padding-bottom: 5px;" src="{$tbiro_liveurl}/calculators/assets/img/buttons/{$tbiro_btn_theme}.png" title="Credit module tbi bank {$tbiro_mod_version}" alt="Credit module tbi bank {$tbiro_mod_version}" onmouseover="this.src='{$tbiro_liveurl}/calculators/assets/img/buttons/{$tbiro_btn_theme}-hover.png'" onmouseout="this.src='{$tbiro_liveurl}/calculators/assets/img/buttons/{$tbiro_btn_theme}.png'">
                {else}
                <a href="{$tbiro_backurl}" target="_blank" title="Go to tbi bank page"><img id="btn_tbiro" style="padding-bottom: 5px;cursor:pointer;" src="{$tbiro_liveurl}/calculators/assets/img/buttons/{$tbiro_btn_theme}.png" title="Credit module tbi bank {$tbiro_mod_version}" alt="Credit module tbi bank {$tbiro_mod_version}" onmouseover="this.src='{$tbiro_liveurl}/calculators/assets/img/buttons/{$tbiro_btn_theme}-hover.png'" onmouseout="this.src='{$tbiro_liveurl}/calculators/assets/img/buttons/{$tbiro_btn_theme}.png'"></a>
                {/if}
            </td>
        </tr>
        <tr>
            <td style="vertical-align:bottom;">
                <p style="color:{$tbi_btn_color}font-size:16pt;font-weight:bold;">{$tbiro_mesecna} {$currency['sign']} x {$tbi_months} {l s='months' d='Modules.Tbiropayment.Front'}</p>
            </td>
        </tr>
    </table>
    {else}
        <a href="{$tbiro_backurl}" target="_blank" title="Go to tbi bank page"><img id="btn_tbiro" style="padding-bottom: 5px;cursor:pointer;" src="{$tbiro_liveurl}/calculators/assets/img/buttons/{$tbiro_btn_theme}.png" title="Credit module tbi bank {$tbiro_mod_version}" alt="Credit module tbi bank {$tbiro_mod_version}" onmouseover="this.src='{$tbiro_liveurl}/calculators/assets/img/buttons/{$tbiro_btn_theme}-hover.png'" onmouseout="this.src='{$tbiro_liveurl}/calculators/assets/img/buttons/{$tbiro_btn_theme}.png'"></a>
    {/if}
{/if}
{/if}
{/if}