<div class="panel">
    <h3>{l s='HS Codes' mod='hscodes'}</h3>
    <table class="table">
        <thead>
        <tr>
            <th>{l s='Reference' mod='hscodes'}</th>
            <th>{l s='Product name' mod='hscodes'}</th>
            <th>{l s='HS Code' mod='hscodes'}</th>
            <th>{l s='Country of origin' mod='hscodes'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$prods item=prod}
            <tr>
                <td>{$prod.product_reference|default:''|escape:'html':'UTF-8'}</td>
                <td>{$prod.product_name|default:''|escape:'html':'UTF-8'}</td>
                <td>{$prod.hscode|default:''|escape:'html':'UTF-8'}</td>
                <td>{$prod.origin|default:''|escape:'html':'UTF-8'}</td>
            </tr>
        {foreachelse}
            <tr><td colspan="4" class="text-muted text-center">{l s='No products found.' mod='hscodes'}</td></tr>
        {/foreach}
        </tbody>
    </table>
</div>
