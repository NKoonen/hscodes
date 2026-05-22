<div class="card mt-3">
    <div class="card-header">
        {l s='HS Codes' mod='hscodes'}
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="hscode">{l s='HS Code' mod='hscodes'}</label>
            <input
                type="text"
                name="hscode"
                id="hscode"
                maxlength="32"
                class="form-control"
                value="{$hscode|escape:'html':'UTF-8'}"
                placeholder="84713000"
            />
        </div>

        <div class="form-group">
            <label for="origin">{l s='Country of origin' mod='hscodes'}</label>
            <input
                type="text"
                name="origin"
                id="origin"
                maxlength="255"
                class="form-control"
                value="{$origin|escape:'html':'UTF-8'}"
                placeholder="{l s='Netherlands' mod='hscodes'}"
            />
        </div>
    </div>
</div>
