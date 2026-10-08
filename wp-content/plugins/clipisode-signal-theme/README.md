# Signal — Clipisode video theme

Signal is an animated Q&A theme for technical conversations. It ships as a separate WordPress plugin and requires Clipisode to be active.

Select **Signal** in a Clipisode composition. Set the series, episode, and topic labels; write a lead question and closing thought; then set each clip to **Question**, **Answer**, or **Insight**. Clip prompts, speaker roles, and key points are optional. The editor exposes all colors and video fit without changing code.

The theme renders a kinetic opening question, a restrained broadcast-style overlay during clips, and a closing thought. It adapts to portrait, square, and landscape compositions. `theme.json` defines every field and the timeline; `renderer.js` implements the visuals with the React and Remotion instances supplied by Clipisode. No build step or external assets are required.

To change the design, edit the two files in this plugin and bump the version in `theme.json`, `clipisode-signal-theme.php`, and its renderer URL. Saved compositions record their theme version and will need to be updated to the new version before rendering.
