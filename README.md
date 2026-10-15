# WLF Agri Parts

A responsive English/Turkish B2B agricultural spare-parts website, redesigned from the user's home-page and catalogue references. Prices, checkout and dealer login are not part of this site.

## Run locally

No dependency installation or build is required. All fonts and visual assets work without third-party runtime requests.

```sh
cd /workspace/web
python3 -m http.server 3000 --bind 0.0.0.0
```

Entry points: `index.html`, `products.html`, `about.html`, `quote.html`, `contact.html`. All paths are relative, so the same files support a GitHub Pages project path such as `/web/` or other static HTTPS hosting. Publish from the repository root.

## Catalogue and search

`catalog-data.js` contains 12 **illustrative** records with local technical SVG artwork. WLF codes, OEM references, model associations, specifications and sourcing states are samples, not a verified commercial catalogue. Replace these records and illustrations with approved product information and actual product photos before commercial use. Do not infer stock, origin or guaranteed compatibility from this demonstration.

- Search by part name in either language, OEM number, WLF code, brand or model.
- Reference searches tolerate spaces and punctuation; text searches normalize Turkish characters.
- Category, machine brand, component group and sourcing filters combine across groups; selections within one group use OR.
- Filter counts describe the complete sample dataset. Search/filter/sort choices and product-detail links can be shared through URL parameters.
- Product details include sample specifications, model references and an add-to-enquiry action.

Language and selected part IDs/quantities persist in this browser's local storage. Contact information and free-text requirements are not saved. Cross-tab list updates preserve an open enquiry form's contact fields.

## Quotation enquiries

Visitors can build a multi-part enquiry and adjust quantities. The required contact fields and requirements are validated before creating an enquiry.

The email action opens the visitor's email application with a draft to **sales@wlfagriparts.com**. The visitor must send it there; the site does not send or store submissions on a backend. A plain-text download is offered as a fallback. Long email drafts automatically use a download to avoid email-client URL limits. There is no false “message sent” confirmation.

Before launch, verify the mailbox and delivery using an actual email client. Automatic form delivery requires a separately configured backend/service; do not put email credentials into browser code.

## Visual assets

- `assets/logo.svg`: original WLF Agri Parts concept logo inspired by the supplied brand direction; business owner approval still required.
- `assets/hero-farm.jpg`: generated illustrative farm/tractor image, not a photograph of company equipment or premises.
- `assets/parts/*.svg`: original illustrative technical product artwork.
- `assets/tractor.svg`: retained from the previous concept; unused by the current pages.

## Validation performed

Chromium functional checks covered home search navigation, OEM/WLF normalization, Turkish and model queries, combined filters, URL restoration, sorting, empty/reset states, product details, language persistence, enquiry/quantity persistence, required fields and invalid quantities, download contents, correct mailto recipient/body, long-enquiry fallback, removal, and cross-tab form preservation. No real outgoing email was sent.

All five pages were checked at widths 360, 390, 768, 1024 and 1440px for horizontal overflow and browser errors. Mobile menu, collapsible filters and product detail were exercised. Local HTML links and assets resolve, and both JavaScript files pass `node --check`.

## Remaining business setup

Replace sample products, confirm logo and company copy, and add approved company/contact/legal details. Hosting publication, domain/DNS connection and actual mailbox delivery are separate from a successful local development run.
