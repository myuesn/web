WLF Agri Parts theme — WordPress 6.5+, PHP 8.0+

INSTALLATION
1. Install and activate WLF Agri Core first (Plugins → Add New → Upload Plugin).
2. Upload this theme ZIP in Appearance → Themes → Add New → Upload Theme.
3. Activate the theme, then open Appearance → WLF Başlangıç.
4. Create the five starter pages. Check the homepage box only when you want
   the new WLF Home page to replace the site's current front page.

Activation alone does not import pages/products or change the current homepage.
The starter import creates new WLF pages and does not overwrite existing copy.
Repeating it reuses its own existing pages. Product records are never imported
automatically. Existing WordPress content and database remain in place.

DAILY EDITING
Pages → Edit opens the native WordPress block editor. Titles, paragraphs,
images, buttons, groups and the order of sections are editable visually.
Use List View to move or remove sections. Functional catalogue and enquiry
areas use normal Shortcode blocks rather than a large Custom HTML block.

For a heading, paragraph or button, English is edited directly in its block.
Select the block and open “WLF — Dil çevirileri” in the right sidebar to edit
Spanish and Russian. An empty translation falls back to English. Review those
translations when changing the English copy. Turkish is only an admin help
language; visitor language options are English, Español and Русский.

Appearance → Customize → WLF — İletişim ve marka contains the business phone,
WhatsApp number, WhatsApp visibility checkbox, sales email, header/footer logos
and footer text in all three languages. The phone and WhatsApp numbers start
empty. No sample phone number is published. Enter the country code and enable
WhatsApp to show the floating button. Reduced-motion preferences are respected.

Change the hero image on an existing page by selecting its image block.
The Customizer hero field only provides the default for new starter pages.
Site Identity → Site Icon can replace the supplied favicon.

Appearance → Menus manages primary/footer menus. If no menu is assigned,
the theme lists its five WLF pages with translated default labels.

PRODUCTS / ENQUIRIES
Use the WLF Agri Core plugin's product and enquiry admin screens.
Publish a product to show it in the catalogue; draft/trash removes it publicly.
Product prices are not shown. The theme does not invent stock availability.
Enquiry submission uses the plugin's REST endpoint and stores the enquiry
privately in WordPress while attempting the sales notification email.
Test delivery on your real hosting; configure its supported SMTP service if
needed. A recorded enquiry and a delivered email are separate outcomes.

FUNCTIONAL SHORTCODES (one instance per page)
[wlf_home_search]       Home catalogue search
[wlf_catalogue]         Search, categories, brands, groups and catalogue
[wlf_featured]          Featured published products
[wlf_enquiry]           Quantity list and enquiry form
[wlf_contact_details]   Sales email and optional phone card

Without WLF Agri Core, pages remain visible but catalogue inventory is empty
and enquiry submission cannot be processed. An admin warning identifies it.

The supplied copy is a starting design. Review it for your actual business.
Optional sample catalogue content is illustrative; confirm all references,
compatibility and specifications before replacing it with real products.
