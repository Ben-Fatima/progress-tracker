<?php

/**
 * Seed Hana Atelier — Content Batch 1: 10 videos sub-category + tasks + docs.
 *
 * Run: php _seed_hana_content.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Step;
use App\Models\Document;

$parent = Category::where('name', 'like', '%Hana Atelier%')->firstOrFail();
echo "Parent: {$parent->name} (id={$parent->id})\n";

$batch = Category::create([
    'name' => 'Hana — Content Batch 1: First 10 Videos',
    'description' => 'Planner box launch content: 10 reels for IG + TikTok, batch-filmed, 1/day release',
    'color' => '#A855F7', // purple to match the box
    'priority' => 'high',
    'deadline' => '2026-10-25',
    'sort_order' => 0,
    'parent_id' => $parent->id,
]);
echo "Created: {$batch->name} (id={$batch->id})\n\n";

$substeps = [
    ['title' => 'Prep props & setup (tripod, lighting, props list)', 'estimated_minutes' => 20],
    ['title' => 'Film all shots', 'estimated_minutes' => 45],
    ['title' => 'Edit in CapCut (crop 9:16, cuts, text overlays, audio)', 'estimated_minutes' => 60],
    ['title' => 'Write caption + hashtags', 'estimated_minutes' => 15],
    ['title' => 'Post to IG Reels + TikTok', 'estimated_minutes' => 10],
];

$videos = [
    [
        'title' => 'V1 — Unboxing ASMR',
        'doc' => <<<'MD'
# V1 — Unboxing ASMR

## Concept
The scroll-stopper. Purple flowery box opens, tissue peels back, three items revealed one by one.

## Format
- 20-30s, ASMR + trending calm audio, no voice
- Post to IG Reels + TikTok

## Shot list
1. Box on table, closed, soft light — hold 2s
2. Hands lift lid slowly — box opening sound
3. Tissue paper peeled back — crinkle ASMR
4. Planner lifted out, placed beside box — 2s hold on floral cover
5. Todolist lifted, A6 in palm for size scale
6. Notebook lifted, hand fans pages quickly
7. Flat-lay: all three items arranged beside open box
8. Close-up: pen writes "Hana" on planner first page

## Text overlays
- 0-3s: "البلانر اللي غادي يبدل يومياتك 📦"
- Final frame: "Hana Atelier" + DM icon

## Audio
Trending calm/soft audio OR pure ASMR (box, tissue, page sounds)

## Caption
```
صندوق التنظيم وصل 📦✨
بلانر + ليستة مهام + نوتبوك
كل شي بعربية، مصمم ليك انتي

📩 للطلب: ابعثينا DM
#planner #organization #تنظيم #stationery #journalmaroc
```

## Notes
- Natural daylight by window, purple/beige props for cohesion
- No face, hands only — rings/nails optional styling choice by Hana
MD
    ],
    [
        'title' => 'V2 — ما كتسناش يناير (undated planner)',
        'doc' => <<<'MD'
# V2 — مازال تسني يناير؟

## Concept
Anti-waiting POV. It's not January — but she starts anyway. The undated planner means your year begins when YOU begin.

## Format
- 20-35s, voiceover (Hana or FZ voice, Darija) + b-roll
- Post to IG Reels + TikTok

## Shot list
1. Calendar/wall showing a mid-year month — slight push in
2. Hand sighs, pushes away old half-used dated planner (or stack of sticky notes)
3. Purple planner opened to month pick page — finger picks current month
4. Hand writes month name + first goals in first pages
5. Montage: week spread being filled quickly (speed ramp)
6. Final: planner closed, hand rests on it — settled

## Voiceover script (Darija)
"مازال تسني يناير باش تبدا تنظمي حياتك؟ هاد البلانر ما عندو تاريخ — تبداي فالشهر اللي بغيتي، و يمشي معاك عام كامل من ديك الساعة. ما كايناش بداية مثالية. كاين غير البداية."

## Text overlays
- Hook: "مازال تسني يناير؟"
- Mid: "بلانر بلا تاريخ = تبداي فوقتاش ما بغيتي"
- End: "✨ Hana Atelier"

## Caption
```
راه البداية الصحيحة هي ليوما 🌱
بلانر بلا تاريخ — تبداي فينهما بغيتي

📩 ابعثينا DM للطلب
#planner #تنظيم #newstart #habits
```

## Notes
- Key differentiator — repeat this angle monthly in future content
- Keep tone encouraging, not preachy
MD
    ],
    [
        'title' => 'V3 — البلانر اللي كيجمع دينك و دينياك (Islamic pages)',
        'doc' => <<<'MD'
# V3 — Islamic Planning Reveal

## Concept
The differentiator no competitor has: the planner tracks your deen alongside your day — prayers, sada9a, spiritual goals.

## Format
- 15-25s, NATURAL SOUND ONLY (page turning, pen) — NO MUSIC
- Post to IG Reels + TikTok

## Shot list
1. Slow open to Islamic monthly planning pages — soft natural light
2. Close-up pan across prayers tracker rows
3. Pen ticks one prayer box — hold on the checkmark
4. Close on sada9a / acts section — pen adds an entry
5. Slow pull-back: full spread visible
6. Final frame: hand rests quietly on page

## Text overlays (minimal, let content breathe)
- Hook: "ماشي غير تنظيم… عبادة"
- End: "Hana Atelier ✨"

## Audio
Natural room tone + pen + page sounds only. NO trending music.
(Optional: very soft ambient nasheed-style instrumental only if brand-approved — default: natural sound)

## Caption
```
بلانر كيجمع دينك و دنياك 🤲
صلاتك، صدقتك، أهدافك — كلشي فبلاصة وحدة

#planner #deen #تنظيم #islamicplanner
```

## Notes
- Respectful pacing — slow, no speed ramps here
- This video targets deep resonance, not virality — expect strong shares/saves within the community
MD
    ],
    [
        'title' => 'V4 — A Day Planned in 60 Seconds (fill-in ASMR)',
        'doc' => <<<'MD'
# V4 — هكا كنحضر أسبوعي

## Concept
Watch a real weekly spread come to life: tasks, prayer times, savings entry — satisfying planning ASMR.

## Format
- 30-40s ASMR + speed ramp, optional soft audio
- Post to IG Reels + TikTok

## Shot list
1. Blank weekly spread, pen poised — 2s hold
2. Speed ramp: Monday column fills (tasks, appointment)
3. Real-time: prayer times written carefully (slow, satisfying)
4. Speed ramp: rest of week fills
5. Real-time: savings note + one sada9a entry
6. Pull-back: completed spread, hands adjust it slightly
7. Final: satisfied tap of pen on page

## Text overlays
- Hook: "هكا كنحضر أسبوعي ✍️"
- End: "Hana Atelier"

## Caption
```
أسبوع كامل فصفحة وحدة 🗓️
مهام، صلوات، توفير — كلشي منظم

📩 للطلب: DM
#planner #weeklyspread #تنظيم #planwithme
```

## Notes
- Speed ramp ratio: ~3x on writing, real-time on satisfying details
- Shoot at golden hour or with warm lamp for cozy feel
MD
    ],
    [
        'title' => 'V5 — Before / After (transformation)',
        'doc' => <<<'MD'
# V5 — أنا قبل / أنا من بعد

## Concept
Relatable transformation: chaos (sticky notes everywhere, forgotten tasks) → one purple box, everything in its place.

## Format
- 20-30s, text-over POV or short voiceover, punchy edits
- Post to IG Reels + TikTok

## Shot list — PART A (chaos, ~8s)
1. Messy desk: sticky notes on laptop, fridge, mirror
2. Hand frantically searching through papers
3. Alarm/calendar notification missed on phone

## Shot list — PART B (order, ~15s)
4. Clean desk: purple box placed down — satisfying thud
5. Items laid out neatly
6. Hand calmly fills todolist
7. Ticks tasks — multiple satisfying checkmarks
8. Final: clean desk + planner open + tea — "done" feeling

## Text overlays
- PART A: "أنا قبل" 😩 / "فوضى فكلشي"
- Transition: "من بعد Hana Atelier ✨"
- End: "النظام فصندوق واحد"

## Caption
```
الفرق بين الفوضى و التنظيم = صندوق واحد 📦

📩 للطلب: DM
#organization #تنظيم #planner #transformation
```

## Notes
- PART A can be comedic — exaggerate the chaos slightly
- Strong transition cut: snap/whip from mess to clean
MD
    ],
    [
        'title' => 'V6 — كيفاش نوفر 500 درهم هاد الشهر (money planning)',
        'doc' => <<<'MD'
# V6 — واش تسالي الشهر و ما عرفتي فين مشات الفلوس؟

## Concept
Money management feature: fill a real monthly budget in the savings planning pages. Speaks to working women directly.

## Format
- 25-35s, voiceover tutorial (Darija) + close-up shots
- Post to IG Reels + TikTok

## Shot list
1. Open to savings/money planning pages
2. Close: income row filled in
3. Close: expenses listed (rent, transport, food)
4. Close: savings goal written — "500 DH"
5. Montage: weekly check-in boxes ticked
6. Final: envelope/jar gesture or just the finished page

## Voiceover script (Darija)
"واش تسالي الشهر و ما عرفتي فين مشات الفلوس؟ هادي الصفحة اللي بدلت ليا. دخلي كتبتو لحدا، المصاريف لحدا، و الهدف ديالي: 500 درهم. كل أسبوع كنراجع و كنعلم. التوفير ماشي قصة كبيرة — قصة صغيرة كتتعاود."

## Text overlays
- Hook: "فين مشات الفلوس؟ 💸"
- Mid: "خطة التوفير داخل البلانر"
- End: "Hana Atelier"

## Caption
```
التوفير كيبدا بالتخطيط 💰
صفحة مخصصة للفلوس داخل البلانر

📩 للطلب: DM
#savings #توفير #planner #moneytips
```

## Notes
- Use realistic Moroccan expenses in Darija (كراء، ترونسبور، القفة)
- Never reveal real income — use round illustrative numbers
MD
    ],
    [
        'title' => 'V7 — The Quote Pages (todolist feature)',
        'doc' => <<<'MD'
# V7 — كل صفحة فيها ليك message

## Concept
Pure aesthetic: flipping through the A6 todolist, each page has a different motivational quote. Serotonin content.

## Format
- 15-20s ASMR page-flip, trending calm audio
- Post to IG Reels + TikTok

## Shot list
1. Todolist held in palm — A6 size charm
2. Flip page 1 — pause 1.5s on a quote
3. Flip page 2 — pause on another quote
4. Flip page 3 — pause on a third
5. One task written + satisfying checkmark tick
6. Final: todolist closed, placed beside tea/flowers

## Text overlays
- Hook: "كل صفحة فيها ليك message 💌"
- End: "Hana Atelier"

## Caption
```
ليستة مهام بحجم الجيب، بكلمة طيبة فكل صفحة 💜

📩 للطلب: DM
#todolist #stationery #تنظيم #quotes
```

## Notes
- Choose quotes that resonate in Darija/Arabic
- The checkmark tick is the ASMR payoff — get a clean take
MD
    ],
    [
        'title' => 'V8 — Evening Journaling Routine (mood piece)',
        'doc' => <<<'MD'
# V8 — روتين المساء ديالي

## Concept
Sells the feeling, not the product. Dim light, tea, the A5 notebook — glow-up evening ritual energy.

## Format
- 20-30s aesthetic, soft lo-fi or natural sound
- Post to IG Reels + TikTok

## Shot list
1. Evening setup: dim warm lamp, window dark, tea steam
2. Hand reaches for A5 notebook, opens to blank page
3. Close: pen begins writing thoughts — soft scratch sounds
4. 45° shot: hand + page + tea in frame, cozy depth
5. Close: pen caps, notebook closes gently
6. Final: tea sip gesture / hand rests — peaceful end

## Text overlays
- Hook: "روتين المساء ديالي 🌙"
- End: "Hana Atelier ✨"

## Caption
```
قبل النوم: شاي، نوتبوك، و أفكاري 🌙
النوتبوك اللي كيستقبل كل شي

📩 للطلب: DM
#journaling #eveningroutine #تنظيم #selfcare
```

## Notes
- Warm color grade, low light — different vibe from bright day videos
- Optional: light candle if available for extra coziness
MD
    ],
    [
        'title' => 'V9 — 3 طرق باش تستعمل الصفحات المنقطة (tutorial)',
        'doc' => <<<'MD'
# V9 — صفحة خاوية = إبداع مفتوح

## Concept
Utility content for saves: 3 ways to use the blank dotted pages — mood tracker, habit tracker, goal breakdown.

## Format
- 30-40s voiceover tutorial + quick cuts
- Post to IG Reels + TikTok

## Shot list
1. Blank dotted page — "صفحة خاوية"
2. USE 1 — Mood tracker: draw simple grid + color legend, fill a few dots
3. USE 2 — Habit tracker: draw habit rows (قراءة، رياضة، صلاة), tick examples
4. USE 3 — Goal breakdown: big goal written, split into small steps
5. Final: all three pages side by side — "خليك تبدع"

## Voiceover script (Darija)
"الصفحات المنقطة ماشي غير خاويين. تقدري تديري فيهم mood tracker، habit tracker، ولا تقسمي هدف كبير لخطوات صغار. كل شهر، نظام جديد على حسابك."

## Text overlays
- Hook: "3 استعمالات للصفحات المنقطة"
- Numbered: "1. Mood / 2. Habits / 3. Goals"
- End: "Hana Atelier"

## Caption
```
3 طرق باش تستعملي الصفحات المنقطة ✨
سيفيها عندك — غادي تحتاجيها

📩 للطلب: DM
#bulletjournal #habittracker #تنظيم #plannerideas
```

## Notes
- Save-driven content — this format gets bookmarked
- Drawings can be simple — relatable > perfect
MD
    ],
    [
        'title' => 'V10 — شنو كاين داخل الصندوق؟ (sales video)',
        'doc' => <<<'MD'
# V10 — الصندوق الكامل ديال التنظيم 🎁

## Concept
The conversion video. Everything laid out, every item explained, price + CTA. Pin to grid.

## Format
- 20-30s direct product video, clean editing
- Post to IG Reels + TikTok, PIN on IG

## Shot list
1. Flat-lay: full box contents arranged beautifully
2. Item 1 — Planner: point to it + text "بلانر سنة كاملة (بلا تاريخ)"
   - quick flashes: weekly spread, savings page, islamic pages
3. Item 2 — Todolist: "ليستة مهام + كلمة تحفيز فكل صفحة"
4. Item 3 — Notebook: "نوتبوك A5 للجورنالينغ"
5. All three together + price card
6. End card: "📩 ابعثينا DM" + @hana___atelier

## Text overlays
- Hook: "شنو كاين داخل الصندوق؟ 🎁"
- Each item labeled as it appears
- Price + "Livraison partout au Maroc"
- End: "📩 DM للطلب"

## Voiceover (optional, Darija)
"البلانر اللي كيجمع كلشي: تنظيم الأسبوع، الشهر، الفلوس، و حتى دينك. زائد ليستة مهام فيها كلمات تحفيز، و نوتبوك لأفكارك. الصندوق متوفر دابا — ابعثينا DM."

## Caption
```
📦 الصندوق الكامل:
🗓️ بلانر سنة (بلا تاريخ — تبداي فوقتاش بغيتي)
✅ ليستة مهام A6 + كلمة تحفيز فكل صفحة
📓 نوتبوك A5 للكتابة الحرة

💰 الثمن: [PRICE]
🚚 توصيل لجميع المدن
📩 للطلب: DM

#planner #stationerymaroc #تنظيم #hanaplanner
```

## Notes
- This is THE pinned video — revisit and re-shoot if first version underperforms
- Add price comment pinned too if comments ask
MD
    ],
];

foreach ($videos as $i => $v) {
    // Parent step for the video
    $videoStep = Step::create([
        'category_id' => $batch->id,
        'title' => $v['title'],
        'sort_order' => $i,
    ]);

    // Sub-steps: film/edit/post workflow
    foreach ($substeps as $j => $s) {
        Step::create(array_merge($s, [
            'category_id' => $batch->id,
            'parent_id' => $videoStep->id,
            'sort_order' => $j,
        ]));
    }

    // Document with the full video brief
    $doc = Document::create([
        'title' => 'Hana Content — ' . $v['title'],
        'description' => 'Video brief: shot list, script, caption, hashtags',
        'type' => 'plan',
        'category_id' => $batch->id,
    ]);

    // Store the doc content — save to file so it's viewable in DocumentDetail
    $planDir = 'C:\Users\fz.benhammou\Desktop\Projects\Perso\Plan\HanaContent';
    if (!is_dir($planDir)) {
        mkdir($planDir, 0755, true);
    }
    $fileName = 'hana-' . strtolower(str_replace(['—', ' ', '(', ')', '/', ':'], ['', '-', '', '', '-', ''], $v['title'])) . '.md';
    $fileName = preg_replace('/-+/', '-', $fileName);
    $filePath = $planDir . '\\' . $fileName;
    file_put_contents($filePath, $v['doc']);
    $doc->update(['file_path' => $filePath]);

    // Link the video task to its doc
    $videoStep->update(['document_id' => $doc->id]);

    echo "  {$v['title']} — step + " . count($substeps) . " substeps + doc ({$fileName})\n";
}

echo "\n=== Summary ===\n";
echo "  Videos: " . count($videos) . "\n";
echo "  Total steps: " . $batch->steps()->count() . "\n";
echo "  Total docs: " . $batch->documents()->count() . "\n";
echo "\nDone!\n";
