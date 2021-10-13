# @todo

- choose and add the icon set
- add to core/README ("Omeka includes...")
- admin theme support? (CSS)
- documentation

# Potential icon sets:

- [Ionicons](https://github.com/ionic-team/ionicons) (MIT License)
- [Bootstrap Icons](https://github.com/twbs/icons) (MIT License)
- [Material Design Icons](https://github.com/google/material-design-icons) (Apache License 2.0)

Of these sets, Ionicons and Bootstrap would probably be the easiest to add/maintain as submodules since they have a simpler directory structure.

# Example Usage and Details
Some sets, such as Ionicons, use a dash-suffix for variants (e.g. sharp, outline). Depending on the chosen set, the `$variants` param may not be necessary but it could still be useful. Consider the utility of, say, a theme configuration for changing all icon variants at once.

```
<?php
// default usage...
echo icon('accessibility');

// 'sharp' variant...
echo icon('accessibility', 'sharp');

// custom 'omeka' icon in theme 'custom_icons' directory...
echo icon('omeka', null, 'images/custom_icons');

// setting the icon variant using a theme option...
$variant = get_theme_option('icon_style');
echo icon('headset', $variant);
echo icon('folder', $variant);
echo icon('globe', $variant);
?>
```

# Example HTML Output
Unlike SVGs displayed via an `img` tag, *inline* SVG can be styled with CSS.

```
<span class="icon globe outline">
	<svg height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg">
	[...] </svg>
</span>
```

# Default CSS

For most themes (including admin), this would probably be a sufficient base...

```
.icon svg {
	height: 1.5em;
	width: 1.5em;
	vertical-align: middle;
	fill: currentColor;
	transition: 0.25s fill linear;
}
.icon svg path,
.icon svg circle {
	stroke: currentColor;
	transition: 0.25s stroke linear;
}
```
