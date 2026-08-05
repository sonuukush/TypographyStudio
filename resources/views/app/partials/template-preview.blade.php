{{-- Template preview partial — included once per card in the grid --}}
@switch($template->animation_type)

    @case('fade-reveal')
        <div class="preview-text" style="font-family:'Playfair Display',serif; color:{{ $template->primary_color }};">
            Your Text Here
        </div>
        @break

    @case('bounce-baseline')
        <div class="bounce-words-container tpl-bounce-baseline" style="display:flex;flex-wrap:wrap;gap:0 6px;align-items:center;justify-content:center;padding:0 8px;">
            <span class="word-bounce" style="font-family:'Poppins',sans-serif;font-weight:900;color:{{ $template->primary_color }};">Your</span>
            <span class="word-bounce" style="font-family:'Poppins',sans-serif;font-weight:900;color:{{ $template->secondary_color }};">Text</span>
            <span class="word-bounce" style="font-family:'Poppins',sans-serif;font-weight:900;color:{{ $template->primary_color }};">Here</span>
        </div>
        @break

    @case('big-small-stack')
        <div class="stack-container">
            <div class="big-line">Your Text</div>
            <div class="small-line">Here</div>
        </div>
        @break

    @case('script-serif-combo')
        <div class="combo-container">
            <div class="script-line">Your</div>
            <div class="serif-line">Text Here</div>
        </div>
        @break

    @case('color-highlight-split')
        <div class="words-container" data-split="color">
            <div class="colored-word" style="color:#FFFFFF; animation-delay:0s;">Your</div>
            <div class="colored-word" style="color:#F72585; animation-delay:0.2s;">Text</div>
            <div class="colored-word" style="color:#FFD60A; animation-delay:0.4s;">Goes</div>
            <div class="colored-word" style="color:#4CC9F0; animation-delay:0.6s;">Here</div>
        </div>
        @break

    @case('center-glow-focus')
        <div class="glow-text">Your Text Here</div>
        @break

    @case('rotate-in-transition')
        <div class="preview-text" style="font-family:'Bebas Neue',sans-serif;color:{{ $template->primary_color }};">
            Your Text Here
        </div>
        @break

    @case('watermark-background')
        <div class="watermark-wrap">
            <div class="bg-text">TYPOGRAPHY</div>
            <div class="fg-text">Your Text Here</div>
        </div>
        @break

    @case('all-caps-bold-display')
        <div class="preview-text" style="font-family:'Fraunces',serif;font-weight:900;color:{{ $template->primary_color }};text-transform:uppercase;">
            YOUR TEXT HERE
        </div>
        @break

    @case('typewriter-reveal')
        <div class="typewriter-text">Your Text Here_</div>
        @break

    @case('heart-accent')
        <div class="heart-container">
            <div class="heart-emoji">💕</div>
            <div class="heart-text">Your Text Here</div>
            <div class="heart-emoji">💕</div>
        </div>
        @break

    @case('neon-glow')
        <div class="neon-text">YOUR TEXT</div>
        @break

    @default
        <div class="preview-text" style="color:{{ $template->primary_color }};">Your Text Here</div>
@endswitch
