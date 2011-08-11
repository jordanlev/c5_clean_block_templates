# Concrete5 Clean Block Templates
This repository contains alternate view.php files for C5's built-in Autonav, Form, and Page List blocks. The templates are intended to serve as a starting point for customization by a designer or developer.

The form template is exactly the same as the "Form Tableless Layout" addon available in the C5 marketplace.

The page_list template should be fairly self-explanatory.

The autonav template is a bit different than the other two. Due to the complexity of the autonav menu structure, it was not possible to make the html clean enough for a designer (or any human being for that matter) to work with, so instead there are a bunch of options at the top of the file where you can set various CSS classes, non-semantic markup additions, and C5 page attributes that will then be magically inserted into the right place in the menu structure. In addition to these markup settings, this template also includes the functionality from the "Autonav Exclude Subpages" addon, so excluding a page from the nav menu will also exclude all of its children pages, and you can also add another attribute that lets you exclude all of a page's children from the nav menu without excluding that page itself.

All of the templates should work with Concrete5 versions 5.4.1 and 5.4.2. The form and page_list templates have been heavily tested in production, whereas the autonav template is currently somewhat experimental and should be thoroughly tested before using in production.
