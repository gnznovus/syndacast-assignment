# MUU Theme Controller

Theme-owned components supporting Elementor Free, Contact Form 7, and Flamingo.

## Navbar + Left Tab

1. Activate the **MUU Hotel Assessment** theme.
2. Configure components from **MUU Controller** in the WordPress dashboard sidebar.
3. On the Elementor hero container, open **Advanced -> CSS Classes** and add `muu-hero-host`.
4. Add Elementor's **Shortcode** widget anywhere on the page.
5. Enter `[muu_nav_lefttab]`.

The shortcode output is absolutely positioned and does not consume layout height. It finds `.muu-hero-host`, moves the rendered component into that container, and uses the target container's rendered image dimensions.

Optional class and target selector:

```text
[muu_nav_lefttab class="my-extra-class" target=".another-hero-class"]
```

Render only the navbar:

```text
[muu_nav_lefttab left-tab="false"]
```

Override navbar colors for a light background:

```text
[muu_nav_lefttab left-tab="false" logo-color="#000000" nav-color="#000000" divider-color="#d9d9d9"]
```

Underscore aliases are supported for `left_tab`, `logo_color`, `nav_color`, and `divider_color`.

## Orange Shape

```text
[muu_orange_shape target=".muu-hero-host" left="20%" top="12%" width="28%" rotate="0" flip_x="true" color="#ff6a00"]
```

## Contact Form

Render and style a Contact Form 7 form without a heading or social icons:

```text
[muu_contact_form id="254"]
```

Contact Form 7 handles validation and mail; Flamingo stores the submission.
