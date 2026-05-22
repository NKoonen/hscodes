# HS Codes

Adds an HS code and country of origin field to products in the PrestaShop back office.

## Compatibility

Tested/updated for the PrestaShop module structure used by PrestaShop 1.7, 8 and 9.

For PrestaShop 8.1 and 9 the module uses the modern product form hooks:

- `actionProductFormBuilderModifier`
- `actionAfterCreateProductFormHandler`
- `actionAfterUpdateProductFormHandler`

For older product pages it keeps the legacy fallback hooks:

- `displayAdminProductsExtra`
- `displayAdminProductsMainStepLeftColumnMiddle`
- `actionProductUpdate`
- `actionAdminProductsControllerSaveAfter`

## Database

The module adds these columns to `ps_product`:

- `hscode` as `VARCHAR(32)`
- `origin` as `VARCHAR(255)`

`hscode` is stored as text, not as an integer, so leading zeroes and formatted customs codes are not lost.

## PDF templates

The `pdf` folder contains example invoice and delivery slip templates. Copy these files to your theme's PDF override folder only if you want HS code and origin shown on generated PDFs.

## Important after installation or upgrade

After installing/upgrading in PrestaShop, clear the cache. If your shop uses overrides, make sure PrestaShop regenerates the class index.
