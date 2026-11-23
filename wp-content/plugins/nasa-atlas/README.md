# NASA Atlas

Isolated NASA / Remote Data Blocks demonstration for maxschmeling.com. Planned with Astra against RDB v1.9.0-alpha.1; implemented as ordinary editable WordPress blocks and a companion plugin. Does not replace the active theme, change menus, or alter existing pages.

## Setup

1. Deploy and activate the published Remote Data Blocks **1.9.0-alpha.1** package.
2. Activate **NASA Atlas — Remote Data Blocks Demo**.
3. In **Tools → NASA Atlas**, click **Create missing NASA demo pages**. This prepares all remote records first, refuses to overwrite unrelated slugs, records ownership, and preserves edits to existing demo pages. Page creation is an explicit administrator action, never automatic on every request or plugin activation.

Pages: `/nasa-atlas/`, `/nasa-atlas/explore/`, `/nasa-atlas/image/`, `/nasa-atlas/editor-guide/`. They use a plugin-provided template and `noindex`. CSS loads only for that template; editor styling loads only for its posts. No global design or homepage options change.

## Features

- Real keyless NASA Image and Video Library HTTP data source; bounded safe input and one-hour metadata caching.
- NASA Image: search selector, browse/list selector, manual ID; three reusable editable patterns.
- NASA Archive: collection render query, repeated core-block template, native RDB pagination, empty/error fallbacks.
- Core Image URL/alt, Heading/Paragraph content, Button URL/text bindings, plus inline rich-text credit binding on the guide.
- Custom Record facts block demonstrates discovered custom attributes and preserved integer/boolean values from actual image records.
- Opt-in URL overrides for public archive search and image detail. A comparison image deliberately remains pinned.
- Visible original-record links, creator credits, and honest archive-date labels. Not an official NASA site.

## Alpha compatibility bridge

The release declares its `remoteData` attribute in the client registration but not server metadata. The companion adds the server object schema **only to its two NASA block names** so WordPress can pass `providesContext` into item templates/pagination/fallbacks. The published RDB package is unmodified. Selection queries use named subclasses because this alpha keys selectors by query class; ordinary identical classes collide.

## Verification / rollback

Verify editor block validity and save/reload, live query search + pagination, image parameter isolation, integer/boolean bindings, inline credit, mobile overflow, and unchanged baseline page hashes/settings/plugins. Confirm live artifact bytes, not merely a GitHub commit.

Rollback: unpublish only the four page IDs in `nasa_atlas_page_ids` (also marked `_nasa_atlas_owned=yes`), deactivate NASA Atlas, and deactivate RDB only if no other content uses it. Remove only these two added plugin directories through the normal deployment workflow. Existing pages and theme need no restoration.

RDB artifact: GitHub release `v1.9.0-alpha.1/remote-data-blocks.zip`, SHA-256 `51284180f15ef6a7dd178b728e3502f01cdf072bd5f0e99a2ec5fdb168c95cc2`.

NASA API documentation: https://images.nasa.gov/docs/images.nasa.gov_api_docs.pdf
