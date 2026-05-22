<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-5 text-right">
			<img width="75px" src="{$module_dir|escape:'html':'UTF-8'}logo.png"/>
		</div>
		<div class="col-xs-7 text-left">
			<h2>{l s='HS Codes' mod='hscodes'}</h2>
			<h4>
                {l s='Donations are welcome' mod='hscodes'}
				<a href="https://www.paypal.com/paypalme/buymecoffee" target="_blank">here</a>
			</h4>
		</div>
	</div>

	<div class="row moduleconfig-header">
		<div class="col-xs-12">
			<center>
				<h4>{l s='This module allows you to insert a HS code for each product. This is required for international sales.' mod='hscodes'}</h4>
				<h4>More info <a href="https://customs-documents.com" target="_blank">https://customs-documents.com</a></h4>

				<p style="margin-top: 20px;">
					<a href="{$missing_hscode_products_url|escape:'html':'UTF-8'}" class="btn btn-primary">
						<i class="icon-search"></i>
                        {l s='Display missing HS code products' mod='hscodes'}
					</a>
				</p>
			</center>
		</div>
	</div>
</div>

{if $show_missing_hscode_products}
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-list"></i>
            {l s='Products without HS code' mod='hscodes'}
		</div>

        {if $missing_hscode_products|count > 0}
			<div class="alert alert-info">
                {l s='These products do not have an HS code filled in yet.' mod='hscodes'}
			</div>

			<div class="table-responsive">
				<table class="table">
					<thead>
					<tr>
						<th>{l s='ID' mod='hscodes'}</th>
						<th>{l s='Product name' mod='hscodes'}</th>
						<th>{l s='Reference' mod='hscodes'}</th>
						<th>{l s='Status' mod='hscodes'}</th>
						<th class="text-right">{l s='Action' mod='hscodes'}</th>
					</tr>
					</thead>
					<tbody>
                    {foreach from=$missing_hscode_products item=product}
						<tr>
							<td>{$product.id_product|intval}</td>
							<td>
                                {if $product.name}
                                    {$product.name|escape:'html':'UTF-8'}
                                {else}
									<em>{l s='No product name found' mod='hscodes'}</em>
                                {/if}
							</td>
							<td>
                                {if $product.reference}
                                    {$product.reference|escape:'html':'UTF-8'}
                                {else}
									<em>-</em>
                                {/if}
							</td>
							<td>
                                {if $product.active}
									<span class="label label-success">{l s='Active' mod='hscodes'}</span>
                                {else}
									<span class="label label-default">{l s='Inactive' mod='hscodes'}</span>
                                {/if}
							</td>
							<td class="text-right">
								<a href="{$product.edit_url|escape:'html':'UTF-8'}" class="btn btn-default btn-sm">
									<i class="icon-pencil"></i>
                                    {l s='Edit product' mod='hscodes'}
								</a>
							</td>
						</tr>
                    {/foreach}
					</tbody>
				</table>
			</div>
        {else}
			<div class="alert alert-success">
                {l s='All products have an HS code filled in.' mod='hscodes'}
			</div>
        {/if}

		<p>
			<a href="{$configure_url|escape:'html':'UTF-8'}" class="btn btn-default">
				<i class="icon-arrow-left"></i>
                {l s='Back to module configuration' mod='hscodes'}
			</a>
		</p>
	</div>
{/if}