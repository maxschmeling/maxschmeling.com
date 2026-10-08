( function () {
	const { React, Remotion, registerRenderer } = window.ClipisodeThemeAPI;
	const { createElement: el } = React;
	const { AbsoluteFill, useCurrentFrame, useVideoConfig } = Remotion;
	const sans = '"Avenir Next", "Helvetica Neue", Arial, sans-serif';
	const mono = '"SFMono-Regular", Consolas, "Liberation Mono", monospace';

	function color( hex, alpha ) {
		return `rgba(${ parseInt( hex.slice( 1, 3 ), 16 ) }, ${ parseInt( hex.slice( 3, 5 ), 16 ) }, ${ parseInt( hex.slice( 5, 7 ), 16 ) }, ${ alpha })`;
	}

	function enter( frame, frames ) {
		const progress = Math.min( 1, Math.max( 0, frame / frames ) );
		return 1 - Math.pow( 1 - progress, 3 );
	}

	function SignalBars( { accent, secondary, unit, frame, count = 21 } ) {
		return el(
			'div',
			{
				style: {
					display: 'flex', alignItems: 'center', gap: unit * 0.005,
					height: unit * 0.035, overflow: 'hidden',
				},
			},
			Array.from( { length: count }, ( _, index ) => {
				const wave = Math.abs( Math.sin( index * 0.72 + frame * 0.105 ) );
				return el( 'span', {
					key: index,
					style: {
						width: unit * 0.006,
						height: unit * ( 0.007 + wave * 0.025 ),
						borderRadius: unit * 0.003,
						backgroundColor: index % 5 === 0 ? secondary : accent,
						opacity: 0.5 + wave * 0.5,
					},
				} );
			} )
		);
	}

	function Card( { settings, kind } ) {
		const frame = useCurrentFrame();
		const { fps, width, height } = useVideoConfig();
		const unit = Math.min( width, height );
		const portrait = height > width * 1.15;
		const wide = width > height * 1.25;
		const inset = unit * 0.068;
		const accent = settings.accentColor;
		const secondary = settings.secondaryColor;
		const progress = enter( frame, fps * 0.7 );
		const second = enter( Math.max( 0, frame - fps * 0.18 ), fps * 0.65 );
		const closing = kind === 'ending';
		const headline = closing ? settings.endingText : settings.title;
		const maxTextWidth = wide ? width * 0.63 : width - inset * 2;
		const headlineSize = Math.min(
			unit * ( portrait ? 0.105 : 0.103 ),
			Math.max( unit * 0.055, maxTextWidth * 2.5 / Math.max( headline.length, 19 ) )
		);
		const orbitSize = unit * ( portrait ? 0.74 : 0.79 );
		const orbitTop = portrait ? height * 0.19 : height * 0.08;
		const orbitRight = portrait ? -orbitSize * 0.23 : width * 0.035;

		return el(
			AbsoluteFill,
			{
				style: {
					backgroundColor: settings.backgroundColor,
					color: settings.textColor,
					fontFamily: sans,
					overflow: 'hidden',
				},
			},
			el( 'div', {
				style: {
					position: 'absolute', inset: 0,
					backgroundImage: `linear-gradient(${ color( secondary, 0.12 ) } 1px, transparent 1px), linear-gradient(90deg, ${ color( secondary, 0.12 ) } 1px, transparent 1px)`,
					backgroundSize: `${ unit * 0.075 }px ${ unit * 0.075 }px`,
					transform: `translateY(${ frame * 0.14 }px)`,
				},
			} ),
			el( 'div', {
				style: {
					position: 'absolute', inset: 0,
					background: `radial-gradient(circle at 78% 32%, ${ color( secondary, 0.29 ) }, transparent 44%), linear-gradient(125deg, ${ color( settings.backgroundColor, 0.15 ) }, ${ settings.backgroundColor } 82%)`,
				},
			} ),
			el( 'div', {
				style: {
					position: 'absolute', width: orbitSize, height: orbitSize,
					top: orbitTop, right: orbitRight, borderRadius: '50%',
					border: `${ unit * 0.002 }px solid ${ color( accent, 0.45 ) }`,
					boxShadow: `0 0 ${ unit * 0.09 }px ${ color( secondary, 0.2 ) }, inset 0 0 ${ unit * 0.1 }px ${ color( secondary, 0.1 ) }`,
					transform: `rotate(${ frame * 0.16 }deg) scale(${ 0.9 + progress * 0.1 })`,
				},
			} ),
			el( 'div', {
				style: {
					position: 'absolute', width: orbitSize * 0.78, height: orbitSize * 0.78,
					top: orbitTop + orbitSize * 0.11, right: orbitRight + orbitSize * 0.11,
					borderRadius: '50%', border: `${ unit * 0.0015 }px dashed ${ color( settings.textColor, 0.18 ) }`,
					transform: `rotate(${ -frame * 0.22 }deg)`,
				},
			} ),
			el( 'div', {
					style: {
						position: 'absolute', top: orbitTop - unit * 0.04,
						right: orbitRight + orbitSize * 0.13,
						fontSize: unit * 0.31, fontWeight: 900, lineHeight: 1,
						letterSpacing: '-0.1em', color: color( accent, 0.09 ),
						transform: `translateY(${ ( 1 - progress ) * unit * 0.09 }px)`,
					},
				},
				closing ? '↗' : 'Q/A'
			),
			el( 'div', {
				style: {
					position: 'absolute', left: inset, right: inset, top: inset,
					display: 'flex', alignItems: 'center', justifyContent: 'space-between',
					gap: unit * 0.025,
				},
			},
			el( 'div', { style: { display: 'flex', alignItems: 'center', gap: unit * 0.014 } },
				el( 'span', { style: { width: unit * 0.021, height: unit * 0.021, borderRadius: '50%', backgroundColor: accent, boxShadow: `0 0 ${ unit * 0.022 }px ${ accent }` } } ),
				el( 'span', { style: { fontWeight: 850, fontSize: unit * 0.028, letterSpacing: '0.13em' } }, settings.seriesName )
			),
			el( 'span', { style: { fontFamily: mono, fontSize: unit * 0.018, letterSpacing: '0.11em', color: color( settings.textColor, 0.74 ), textAlign: 'right' } }, settings.episodeLabel )
			),
			el( 'div', {
				style: {
					position: 'absolute', left: inset, right: inset,
					top: portrait ? height * 0.47 : height * 0.29,
					maxWidth: maxTextWidth,
				},
			},
			el( 'div', { style: { display: 'flex', alignItems: 'center', gap: unit * 0.02, marginBottom: unit * 0.047, opacity: second } },
				el( 'span', { style: { backgroundColor: accent, color: settings.backgroundColor, fontFamily: mono, fontSize: unit * 0.019, fontWeight: 800, letterSpacing: '0.12em', padding: `${ unit * 0.009 }px ${ unit * 0.016 }px` } }, closing ? 'OUTRO / 02' : 'QUESTION / 01' ),
				el( 'span', { style: { color: color( settings.textColor, 0.76 ), fontFamily: mono, fontSize: unit * 0.018, letterSpacing: '0.08em' } }, settings.topicLabel )
			),
			el( 'div', {
				style: {
						fontSize: headlineSize, fontWeight: 800, letterSpacing: '-0.035em', lineHeight: 1.02,
						whiteSpace: 'pre-wrap', overflowWrap: 'anywhere',
						maxHeight: portrait ? height * 0.27 : height * 0.39, overflow: 'hidden',
						opacity: progress, transform: `translateY(${ ( 1 - progress ) * unit * 0.065 }px)`,
						textShadow: `0 ${ unit * 0.01 }px ${ unit * 0.04 }px ${ color( settings.backgroundColor, 0.7 ) }`,
					},
				}, headline ),
			! closing && el( 'div', {
				style: {
						fontSize: unit * 0.028, lineHeight: 1.35, maxWidth: maxTextWidth * 0.8,
						marginTop: unit * 0.035, color: color( settings.textColor, 0.75 ),
						whiteSpace: 'pre-wrap', opacity: second,
					},
				}, settings.subtitle )
			),
			el( 'div', {
				style: {
					position: 'absolute', left: inset, right: inset, bottom: inset,
					display: 'flex', justifyContent: 'space-between', alignItems: 'end',
					paddingTop: unit * 0.027, borderTop: `${ unit * 0.002 }px solid ${ color( accent, 0.68 ) }`,
				},
			},
			el( 'div', { style: { display: 'flex', alignItems: 'center', gap: unit * 0.018 } },
				el( 'span', { style: { fontFamily: mono, fontSize: unit * 0.018, letterSpacing: '0.08em', color: accent } }, closing ? 'END TRANSMISSION' : 'OPEN CHANNEL' ),
				el( SignalBars, { accent, secondary, unit, frame } )
			),
			el( 'span', { style: { fontFamily: mono, fontSize: unit * 0.018, color: color( settings.textColor, 0.58 ) } }, 'Q&A / SIGNAL' )
			)
		);
	}

	function Overlay( { settings, name, clip } ) {
		const frame = useCurrentFrame();
		const { fps, width, height } = useVideoConfig();
		const unit = Math.min( width, height );
		const portrait = height > width * 1.15;
		const inset = unit * 0.055;
		const progress = enter( frame, fps * 0.46 );
		const values = clip?.values || {};
		const segmentType = values.segmentType || 'answer';
		const prompt = values.prompt;
		const role = values.speakerRole;
		const keyPoint = values.keyPoint;
		const symbol = segmentType === 'question' ? '?' : segmentType === 'insight' ? '✳' : 'A';
		const label = segmentType === 'question' ? 'QUESTION' : segmentType === 'insight' ? 'INSIGHT' : 'ANSWER';
		const accent = segmentType === 'question' ? settings.secondaryColor : settings.accentColor;

		return el(
			AbsoluteFill,
			{ style: { pointerEvents: 'none', color: settings.textColor, fontFamily: sans } },
			el( 'div', { style: { position: 'absolute', top: 0, left: 0, right: 0, height: height * 0.24, background: `linear-gradient(${ color( settings.backgroundColor, 0.86 ) }, transparent)` } } ),
			el( 'div', { style: { position: 'absolute', bottom: 0, left: 0, right: 0, height: portrait ? height * 0.4 : height * 0.48, background: `linear-gradient(transparent, ${ color( settings.backgroundColor, 0.96 ) })` } } ),
			el( 'div', {
				style: {
					position: 'absolute', top: inset, left: inset, right: inset,
					display: 'flex', alignItems: 'start', justifyContent: 'space-between', gap: unit * 0.03,
				},
			},
			el( 'div', { style: { display: 'flex', alignItems: 'center', gap: unit * 0.017 } },
				el( 'span', { style: { width: unit * 0.017, height: unit * 0.017, borderRadius: '50%', backgroundColor: settings.accentColor, boxShadow: `0 0 ${ unit * 0.025 }px ${ settings.accentColor }`, opacity: 0.7 + Math.sin( frame * 0.14 ) * 0.3 } } ),
				el( 'span', { style: { fontWeight: 850, fontSize: unit * 0.025, letterSpacing: '0.12em' } }, settings.seriesName )
			),
			el( 'div', { style: { textAlign: 'right', fontFamily: mono, fontSize: unit * 0.016, lineHeight: 1.4, letterSpacing: '0.08em', color: color( settings.textColor, 0.84 ) } },
				el( 'div', null, settings.episodeLabel ),
				el( 'div', { style: { color: settings.accentColor } }, settings.topicLabel )
			)
			),
			el( 'div', {
				style: {
					position: 'absolute', left: inset, right: inset, bottom: portrait ? height * 0.12 : inset,
					transform: `translateY(${ ( 1 - progress ) * unit * 0.055 }px)`, opacity: progress,
				},
			},
			el( 'div', { style: { display: 'flex', alignItems: 'center', gap: unit * 0.019, marginBottom: unit * 0.023 } },
				el( 'span', { style: { backgroundColor: accent, color: settings.backgroundColor, fontFamily: mono, fontWeight: 900, fontSize: unit * 0.021, padding: `${ unit * 0.011 }px ${ unit * 0.017 }px` } }, symbol ),
				el( 'span', { style: { fontFamily: mono, fontSize: unit * 0.018, letterSpacing: '0.15em', color: accent } }, label ),
				el( SignalBars, { accent, secondary: settings.secondaryColor, unit, frame, count: portrait ? 16 : 24 } )
			),
			prompt && el( 'div', {
				style: {
					fontSize: unit * ( portrait ? 0.043 : 0.045 ), fontWeight: 750,
					lineHeight: 1.16, letterSpacing: '-0.018em',
					maxWidth: portrait ? width * 0.86 : width * 0.67,
					maxHeight: unit * 0.115, overflow: 'hidden', overflowWrap: 'anywhere',
					marginBottom: unit * 0.026, whiteSpace: 'pre-wrap',
				},
			}, prompt ),
			el( 'div', { style: { display: 'flex', alignItems: 'end', justifyContent: 'space-between', gap: unit * 0.03, borderTop: `${ unit * 0.0015 }px solid ${ color( settings.textColor, 0.45 ) }`, paddingTop: unit * 0.02 } },
				el( 'div', null,
					settings.showNames && el( 'div', { style: { fontSize: unit * 0.034, fontWeight: 730, lineHeight: 1.08 } }, name ),
					role && el( 'div', { style: { marginTop: unit * 0.01, fontFamily: mono, fontSize: unit * 0.017, letterSpacing: '0.08em', color: color( settings.textColor, 0.75 ) } }, role )
				),
				keyPoint && el( 'div', { style: { maxWidth: portrait ? width * 0.4 : width * 0.29, color: accent, fontFamily: mono, fontSize: unit * 0.017, lineHeight: 1.3, letterSpacing: '0.06em', textAlign: 'right', overflowWrap: 'anywhere' } }, `↗ ${ keyPoint }` )
			)
			)
		);
	}

	registerRenderer( 'signal', { Card, Overlay } );
} )();
