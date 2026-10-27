# Kidzio: Build Plan (Laravel TALL stack, mobile-first, Georgian UI)

> **Purpose:** a complete brief for an AI coding agent that builds Kidzio, a Duolingo-style learning game for Georgian school kids (grades 1-4 at launch, model supports 1-12).
> **Read `AGENTS.md` too.** It holds the Laravel Boost rules (Artisan `make:` commands, Pest, Pint, Livewire/Tailwind skills). This file adds the product rules.
> **Build from scratch in this project** (`d:\Herd\klasgareshe-app`). Do not copy HTML/CSS/JS from `kidzio/`.

---

## 0. Rules for the AI agent (read first)

1. Work in the phases of section 13, in order. Finish each phase with passing Pest tests before starting the next.
2. **The app UI is Georgian (`ka`) only at launch.** Still put every string in `lang/ka.json` / `lang/ka/*.php`; never hard-code text in Blade. This lets other languages be added later.
3. **Mobile-first**, designed for 360-430px. On desktop, center the app in a `max-w-[480px]` column.
4. **Content is data, not code.** Subjects, courses, lessons and exercises live in the database. Admins will author them later; for now they come from seeders (section 6). Never hard-code a question in a view.
5. Every game type is a plug-in: one PHP class (validation and answer checking) plus one Blade/Alpine component (UI). See section 5.
6. **Simplicity is the top UI requirement.** Every kid screen passes the 5-second test in section 10.
7. **Payments are out of scope for release 1, but the app must be ready for them** (section 11). Do not write any payment-provider code.
8. **Trace-the-letter game is removed.** Do not build it, and do not seed `config/georgian.php` stroke paths.
9. Use `php artisan make:*` commands, Pest tests, and run `vendor/bin/pint --dirty --format agent` before finishing PHP changes.
10. Ask before adding dependencies beyond section 3.

---

## 1. Product summary

| | |
|---|---|
| **Who plays** | Kids in grades 1-4 at launch. Data model supports grades 1-12 so more grades can be added later by adding content only. |
| **Who owns the account** | A **parent** (email login). One parent has several **child profiles**. |
| **Core loop** | Pick a subject, follow the grade path (units, lessons), each lesson is 6-10 short mini-games, earn stars and XP, keep the streak, unlock badges and avatars. |
| **Two kinds of content** | (1) **Grade path:** the main curriculum per subject and grade. (2) **Themes:** optional topic courses inside a subject (e.g. Math: "საათი და დრო"). |
| **Language** | Georgian only at launch. |
| **Business** | Free in release 1. A paid plan comes later (section 11). |

### Design principles
- **Celebrate proportionally.** Correct answer: short green flash and sound. Finished lesson: one star screen. Big confetti only for milestones (unit done, new badge, 7-day streak).
- **Mistakes are gentle.** Show "სცადე თავიდან", show the right answer, repeat the exercise once at the end of the lesson. **No hearts or lives** in release 1.
- **One currency:** XP shown as ⭐. No gems or coins.
- **Read aloud.** Prompts have a 🔊 button that plays a pre-recorded audio file if one exists; otherwise the button is hidden (browsers usually have no Georgian speech voice).
- **No social free text.** No chat. Usernames are generated, not typed.

---

## 2. The `kidzio/` folder (reference only)

`kidzio/` is a static English HTML mirror (about 77 pages) of an old prototype. Use it only as an idea list.

**Take:** the game mechanics (section 5) and the base color palette.
**Do not take:** layouts, HTML/CSS/JS, hard-coded content, or the information density. The prototype's home screen shows a mission card, streak, XP, league, continue card and plan list at once; a child cannot tell what to tap.

| Prototype feature | In the new app |
|---|---|
| leaderboard, ranking-weekly, ranking-friends, league | **One** simple League tab |
| xp-progress, rewards-dashboard, monthly-goals | Dropped. A level ring on the profile is enough. |
| weekly-report, full-report, export-progress | **One** report screen in the parent zone |
| section-list, lesson-details, lesson-locked, lesson-continue | Home path plus one small "start lesson" sheet |
| daily-mission page | One small card/sheet with 3 ticks |
| streak page | One small sheet: flame, number, this week's 7 dots |
| badges, 9 `badge-unlock*.html` copies | One badge grid and one reusable "new badge" modal |
| app-language, country, clear-cache | Dropped |
| Fake numbers ("1,240 playing", "Top 12%", "Kid rating 4.9") | Dropped |

### Visual system (starting point)
- **Colors:** primary `#7C5CFF` (ink `#4B2FD6`), mint `#4ED6A8`, coral `#FF7A8A`, sun `#FFB547`, sky `#49B8FF`, pink `#FF8FC5`, lemon `#FFE066`. Light: bg `#FFF8F1`, surface `#FFFFFF`, ink `#1B1240`, border `#EDE7F6`. Dark: bg `#0F0B22`, surface `#1A1436`, ink `#F5F1FF`, border `#2A2150`. Correct `#0E7A5A`, wrong `#D2304A`.
- Define them as Tailwind v4 `@theme` tokens. Dark mode = `.dark` class on `<html>`, stored in the child's settings and mirrored in localStorage to avoid a flash.
- Use **one accent color** for the main action, green/red only for answer feedback.
- One icon set (Phosphor, self-hosted via npm, no CDN).

### Fonts (Georgian)
The prototype's Baloo 2 and Nunito have **no Georgian glyphs**. The owner already downloaded the font from typeface.ge and placed it in this project at `public/assets/fonts/`: `alk-life-webfont.{woff2,woff,ttf,eot,svg}` (family name `ALK Life`). There is no `fonts.css`; do not move or copy the files.

**The only font is `ALK Life`, one weight (Regular).** Plan around that:
- **Phase 0 task:** write the `@font-face` in `resources/css/app.css` pointing at `/assets/fonts/alk-life-webfont.woff2` then `.woff` (skip `.eot`/`.svg`/`.ttf`; modern browsers don't need them), with `font-display: swap`. Use it as the single app font (`--font-sans`).
- **No bold file exists.** Do not use `font-bold`/`font-extrabold` with `font-synthesis` on, because the browser fakes a blurry bold. Set `font-synthesis: none` and `font-weight: 400` everywhere; create hierarchy with **size and color**, not weight. (Matches rule: 1 family, max 2 weights.) If a real bold is wanted later, add a second font file; no code change beyond one `@font-face`.
- ⚠️ **First thing to verify (Phase 0):** render "1234567890 + − × ÷ = ? ! . ," and the 33 Georgian letters with ALK Life. If the digits or math symbols are missing, the stack falls back per glyph to `Noto Sans Georgian` (self-hosted, OFL), so keep that as the second family in the font stack.
- ⚠️ **Check the licence allows commercial/web use** before launch (a paid plan is planned).
- **Never use `uppercase`** or `text-transform` on Georgian text in the kid UI (browsers may switch to the Mtavruli capital style, which grade-1 children cannot read).

---

## 3. Tech stack

| Layer | Choice |
|---|---|
| Backend | Latest stable Laravel, PHP 8.4 (as installed) |
| UI | Livewire full-page components + Alpine.js for in-game interaction |
| CSS | Tailwind CSS v4, tokens from section 2 as `@theme` |
| Admin / content CMS | Filament at `/admin` (needs approval before install, per AGENTS.md) |
| DB | MySQL (Herd) |
| Auth | Parent email + password with email verification by **6-digit OTP** (build with Laravel's own features; ask before adding a starter kit package) |
| Queue / cache | `database` driver at first |
| Media | `spatie/laravel-medialibrary` for exercise images and audio (needs approval) |
| PWA | Web manifest + service worker (install to home screen, cache fonts/icons/sounds and the current lesson) |
| Tests | Pest |

Always check installed versions with `composer show --direct` / `package.json` before using an API.

---

## 4. Domain model

### 4.1 Accounts
```
users                     -- parents, the only real login
  id, name, email (unique), password, email_verified_at,
  parent_pin_hash (4 digits), is_admin bool default false,
  locale default 'ka', timezone default 'Asia/Tbilisi',
  timestamps

children                  -- kid profiles
  id, user_id FK, display_name (parent only),
  username (generated, unique), avatar_id FK, avatar_frame_id FK nullable,
  grade_id FK, birth_year nullable,
  xp_total int default 0, level int default 1,
  streak_current int, streak_best int, streak_freezes int default 2,
  last_active_date date nullable,
  leaderboard_enabled bool,           -- default true for grade >= 3
  settings json,                      -- see section 9
  timestamps, soft deletes
```
- **Session model:** the parent logs in, then picks a child on **"ვინ თამაშობს?"** (Who's playing?). The `child_id` is kept in the session. Middleware `EnsureChildSelected` protects kid routes. Switching profile or opening the parent zone needs the **parent PIN**.
- Kids never type an email or password.

### 4.2 Curriculum and content
```
grades      id, number (1..12), name ('I კლასი'), is_active   -- seed 1-4 active, 5-12 inactive
subjects    id, slug, name, icon, color_token, sort, is_active
courses     id, subject_id, kind enum('path','theme'), title, description, icon, cover_media,
            grade_min, grade_max, is_premium bool default false, sort, is_published
units       id, course_id, title, description, sort, icon
lessons     id, unit_id, title, sort, est_minutes, xp_reward default 10,
            is_checkpoint bool, settings json (shuffle, pass_ratio default 0.7)
exercises   id, lesson_id, type (string key), prompt, prompt_audio (media), image (media),
            payload json (type-specific, validated by the type class),
            explanation nullable, difficulty tinyint 1..3, sort, is_active
```
- **Grade path** = `courses.kind = 'path'`, one per (subject, grade), e.g. "მათემატიკა: II კლასი".
- **Theme** = `courses.kind = 'theme'` with a grade range.
- A child sees the paths of **their grade** (plus earlier grades for review) and the themes whose range includes their grade.
- Adding grade 5+ later = activate the grade rows and add courses. No code change.

### 4.3 Progress
```
lesson_attempts    id, child_id, lesson_id, started_at, finished_at,
                   correct_count, wrong_count, stars (0..3), xp_earned, duration_sec
exercise_answers   id, lesson_attempt_id, exercise_id, answer json, is_correct, ms_taken, attempt_no
lesson_progress    child_id + lesson_id (PK), best_stars, completed_at, times_played
xp_events          id, child_id, amount, source enum(lesson, streak_bonus, mission, achievement, league), source_id, created_at
daily_activity     child_id, date, xp, lessons, seconds
```
- **Stars:** finished = 1★, ≥ 80% first-try correct = 2★, 100% = 3★. Replays improve `best_stars`; replay XP is 50%.
- **Unlock:** a lesson unlocks when the previous one has ≥ 1★. The next unit unlocks when the checkpoint lesson is passed. A parent setting "unlock all" overrides this.

### 4.4 Gamification
```
avatars              id, key, emoji_or_image, unlock_rule json nullable (null = free)
avatar_frames        id, key, image, unlock_rule json
achievements         id, key, name, description, icon, rarity enum(common,rare,epic,legend), rule json
child_achievements   child_id, achievement_id, progress int, unlocked_at nullable
daily_missions       id, child_id, date, tasks json, completed_at, reward_xp
leagues              id, tier (1..6: ბრინჯაო, ვერცხლი, ოქრო, ზურმუხტი, საფირონი, ბრილიანტი)
league_weeks         id, league_id, week_start, group_no
league_memberships   league_week_id, child_id, xp_week, final_rank, result enum(promote, stay, demote)
```

### 4.5 Billing tables (create now, leave unused in release 1)
```
plans           id, key ('free','premium'), name, price_gel, interval, features json
subscriptions   id, user_id, plan_id, provider, provider_ref, status, current_period_end, cancelled_at
payments        id, subscription_id, amount_gel, status, provider_payload json
```
See section 11 for what to build and what to leave out.

---

## 5. Exercise engine

**11 exercise types** (the old trace-letter game is removed):

| Type key | Mechanic | Prototype source |
|---|---|---|
| `choice` | Question + options. Variants: `list`, `tiles`, `image_prompt`, `word_prompt`. Covers multiple-choice, tap-correct, match-animal, where-live, opposites, knowledge quiz. | 6 games |
| `match_pairs` | Match left items to right items (variants `tap`, `lines`) | match-word, connect-pair |
| `build_word` | Picture + audio, tap Georgian letter tiles to spell the word | spell-word |
| `fill_gap` | Word/number with one missing piece, pick the answer | fill-letter |
| `number_input` | Count objects or solve a sum, enter a number | counting |
| `riddle` | გამოცანა: clues appear one by one, pick the answer | guess-animal |
| `hotspot` | "Tap the elbow" on a picture, or label buttons | body-parts |
| `word_search` | Find N words in a letter grid | word-search |
| `order` | Drag/tap items into the right order | new |
| `categorize` | Sort items into 2-3 baskets | new |
| `true_false` | Statement → მართალია / მცდარია | new |

### Architecture
```
app/Exercises/
  Contracts/ExerciseType.php
  ExerciseRegistry.php            -- 'choice' => ChoiceExercise::class, ...
  Types/ChoiceExercise.php  ... one class per type
resources/views/components/exercise/
  choice.blade.php  ... one Alpine component per type
```
```php
interface ExerciseType {
    public static function key(): string;
    public static function label(): string;                 // shown in admin
    public function rules(): array;                         // validation rules for payload
    public function filamentSchema(): array;                // admin form fields
    public function clientPayload(Exercise $e): array;      // what the browser receives
    public function check(Exercise $e, mixed $answer): CheckResult;
}
```
- `clientPayload()` includes what Alpine needs for **instant feedback** (no network wait for a 6-year-old).
- **The server is the source of truth:** Alpine then calls `$wire.submitAnswer(...)`; the server runs `check()` again, stores `exercise_answers`, and only the server awards XP.
- The shared lesson shell owns the footer button, feedback sheet, sounds and progress bar. Each component only dispatches `answered` with `{correct, answer}`.

### Payload shapes
```jsonc
// choice
{ "variant": "list|tiles|image_prompt|word_prompt",
  "options": [ {"id":"a","text":"ძროხა","emoji":"🐄","image":null,"audio":null} ],
  "correct": ["a"], "shuffle": true }
// match_pairs (3-5 pairs)
{ "variant": "tap|lines", "pairs": [ {"left":{"text":"მზე"}, "right":{"emoji":"☀️"}} ] }
// build_word
{ "word": "ვაშლი", "emoji": "🍎", "extra_letters": 3, "mark_wrong_positions": true }
// fill_gap
{ "template": "ვა_ლი", "options": ["შ","ს","ჩ","ც"], "correct": "შ", "emoji": "🍎" }
// number_input (counting or arithmetic)
{ "emoji": "🍎", "count": 8, "min": 0, "max": 20 }   // or { "expression": "7 + 5", "answer": 12 }
// riddle
{ "clues": ["...","...","..."], "options": [ ... ], "correct": ["zebra"], "fewer_clues_bonus": true }
// hotspot
{ "image": "media:body.png", "targets": [ {"id":"elbow","label":"იდაყვი","x":62,"y":41,"r":7} ], "ask": "elbow" }
// word_search
{ "size": 6, "words": ["კატა","მზე"], "directions": ["h","v"] }   // grid generated server-side, cached per attempt
// order
{ "items": [ {"id":"1","text":"11"} ], "correct_order": ["1","2","3"] }
// categorize
{ "buckets": [ {"id":"farm","label":"ფერმა"} ], "items": [ {"id":"cow","emoji":"🐄","bucket":"farm"} ] }
// true_false
{ "statement": "პინგვინი ცხელ უდაბნოში ცხოვრობს.", "correct": false }
```

### Georgian language rules
- Georgian has **no upper/lower case.** Compare answers by exact Unicode match after NFC normalization.
- `build_word` tiles use Georgian letters only. Distractors come from `config/georgian.php` (33 letters in order, vowels, and **groups of look-alike/sound-alike letters** such as ც/წ, ჩ/ჭ, კ/ქ, პ/ფ, ტ/თ). No stroke paths needed.
- Use `mb_*` functions and `preg_split('//u', ...)` to split words into letters.

### Audio
- Each exercise may have `prompt_audio`. Each type has a generic instruction audio in `public/audio/instructions/` ("აირჩიე სწორი პასუხი" etc.).
- Missing audio file → hide the 🔊 button. Audio is added later by admin upload or a queued text-to-speech job (provider decided later).

---

## 6. Dummy content (seeders)

Content will be authored by admins later. For now the seeders create **realistic dummy Georgian content** so the app is playable right after `php artisan migrate --seed`. Use emoji as images so no media files are needed.

**Seed contents**
- **Grades:** 1-4 active, 5-12 inactive.
- **Subjects:** `georgian` (ქართული ენა), `math` (მათემატიკა), `nature` (ბუნებისმეტყველება), `society` (მე და საზოგადოება, grades 3-4).
- **Demo course "ყველა თამაში"** (all games): one unit, one lesson per exercise type, **at least 3 exercises per type** (11 types). This is the main way to test and demo every game.
- **Grade 1:** `georgian`, `math`, `nature` path courses with 2 units × 3 lessons × 6-8 exercises, using every type at least once.
- **Grades 2, 3, 4:** one path course per grade for `georgian` and `math`, each 1 unit × 3 lessons × 6 exercises, plus `nature` for grade 2 and `society` for grades 3-4 with 1 unit × 2 lessons.
- **Themes:** 2 (e.g. "საათი და დრო", grades 2-4; "საქართველოს ცხოველები", grades 1-4).
- **Dev data:** one parent user (known dev password), one admin user, two children (grade 1, grade 3), 24 free avatars, about 20 achievements, one league.

Example topics: ანბანი და პირველი სიტყვები, მარცვლები, რიცხვები 1-20, შეკრება/გამოკლება, გამრავლების ტაბულა, ცხოველები და სახლები, სხეულის ნაწილები, ანდაზები, გამოცანები, ანტონიმები, საათი, ლარი და თეთრი, წილადები (grade 4).

⚠️ **The dummy Georgian text is AI-written and must be reviewed by a native speaker/teacher** before real use. Keep it clearly marked as dummy (e.g. each course description starts with "[დემო]").

---

## 7. Lesson player (core screen)

`App\Livewire\LessonPlayer`, route `/lesson/{lesson}`:

1. **Start:** create a `lesson_attempts` row and build the queue of up to 10 active exercises (shuffled if the lesson says so).
2. **Layout:**
   - top: ✕ (confirm "გინდა გასვლა?") + progress bar
   - middle: one prompt with 🔊, then the exercise component
   - bottom: one sticky main button, **შემოწმება** (check), then **გაგრძელება** (continue)
3. **After checking:** a bottom sheet. Correct = green, short sound, one random short praise ("ყოჩაღ!", "სწორია!"). Wrong = soft red, shows the correct answer and the `explanation`; the exercise is re-queued once at the end.
4. **Finish:** result screen with 0-3 stars, XP, and the streak flame if it grew today. One button. Badge unlocks appear afterwards, one at a time.
5. The server fires a `LessonCompleted` event. Listeners: `AwardXp`, `UpdateStreak` (child's timezone), `UpdateDailyActivity`, `EvaluateAchievements`, `UpdateDailyMission`, `AddLeagueXp`, `UpdateLessonProgress`.
6. **Resume:** the home "continue" button resumes an unfinished attempt.
7. Screen-time and bedtime limits are checked when a lesson starts.

Sounds: `correct`, `wrong`, `complete`, `tap`; respect the child's sound setting; `navigator.vibrate(30)` where supported.

---

## 8. Gamification

- **XP:** lesson `xp_reward` (default 10) + 5 for 3★; replays 50%. **Level** = `floor(sqrt(xp_total / 50)) + 1`, shown as a ring around the avatar.
- **Streak:** a day counts when ≥ 1 lesson is finished (Asia/Tbilisi date). 2 streak freezes, auto-used on a missed day, +1 per week up to 2. Milestones at 3/7/14/30/100 days give a badge.
- **Achievements:** rule types `lessons_completed` (optional subject), `perfect_lessons`, `streak_days`, `unit_completed`, `course_completed`, `xp_total`, `exercise_type_correct`, `league_finish`. Some unlock avatars or frames, so cosmetics have a purpose without a second currency.
- **Usernames and safety:** generated from a Georgian adjective + animal + number; the kid can tap 🔄 for another; no free typing. Public data on the league = username, avatar, weekly XP only. Collect minimum personal data; parent consent at signup (checkbox + privacy policy link).
- **League:** groups of up to 20 kids of similar grade (bands 1-2, 3-4). A scheduled `RolloverLeagues` command runs Monday 00:00 Tbilisi: top 3 promote, bottom 3 demote. Off by default for grades 1-2; the parent can toggle it.
- **Daily mission:** 3 simple tasks (finish 1 lesson, answer 10 correctly, play 1 theme lesson). Completing all gives bonus XP and a random avatar item. A small card on home, not a busy page.

---

## 9. Parent zone and settings

Route prefix `/parent`, middleware `auth` + `verified` + parent-PIN (re-asked after 10 minutes idle).

- **Children:** add/edit/remove profiles, grade, username, avatar, "unlock all lessons", leaderboard toggle.
- **Report per child:** 4 numbers (minutes this week, lessons, accuracy, streak), a 7-day bar chart, and up to 3 "needs practice" topics. Weekly email summary (queued).
- **Screen time:** daily limit (10-120 min), bedtime lock (from-to), break reminder. When reached, the kid sees "დროა დავისვენოთ 🌙".
- **Account:** email, password, change PIN, delete account (deletes all child data), subscription area (empty placeholder in release 1).

**Child settings** (`children.settings` json): `theme` (system/light/dark), `accent`, `text_size` (md/lg/xl), `sound_effects`, `voice_reader`, `daily_goal_minutes`, `difficulty` (auto/easy/medium/hard).

---

## 10. Screen design rules (kid UI)

**5-second test:** a slow-reading 6-year-old must know (1) where they are and (2) what to tap within 5 seconds. If not, remove things.

| Rule | Limit |
|---|---|
| Main purpose | 1 per screen |
| Primary button | Exactly 1, full width, at the bottom, accent color |
| Secondary actions | Max 2, plain text or small icons |
| Numbers visible | Max 2 (e.g. 🔥5 and ⭐120). No percentages, "x / y" counters or ranks on home. |
| Text | Max 2 short lines of instruction (≤ 8 words). Every instruction has 🔊. |
| Content blocks | Max 3 visible without scrolling |
| Scrolling | Lesson, result and modals never scroll. Only the path and badge grid may. |
| Colors | Neutral background, one accent, green/red only for feedback |
| Fonts | 1 family, max 2 weights per screen. Prompt ≥ 22px, options ≥ 20px, labels ≥ 16px, nothing under 14px. No uppercase. |
| Touch targets | ≥ 56px for answers and main buttons, ≥ 48px for icons, ≥ 12px gaps |
| Decoration | No patterns, no "NEW/HOT" pills, no notification dots |
| Animation | ≤ 400ms, only for cause and effect, respect `prefers-reduced-motion` |
| Words | Everyday Georgian words a grade-1 child knows. No English or jargon ("XP", "League", "Streak" → ვარსკვლავები, ლიგა, ცეცხლი). |
| Popups | Max 1 at a time, never on top of another |

**Bottom navigation (3 tabs, hidden in lessons):** 🏠 სწავლა · 🏆 ჯილდოები · 🐻 მე

**Complete kid screen list**
1. Who's playing? (avatars, parent lock icon in a corner)
2. Home / path (top bar: subject picker, 🔥, ⭐; then the lesson path; a "themes" row at the end; current node big and pulsing)
3. Subject picker (sheet, max 5 tiles)
4. Start lesson (sheet: title, 3 stars, "დაწყება")
5. Locked lesson (sheet: 🔒 + one sentence)
6. Lesson player
7. Answer feedback (sheet inside the lesson)
8. Lesson result
9. New badge (modal)
10. Themes grid
11. Rewards (tabs: ბეჯები grid, ლიგა list)
12. Me (avatar with level ring, username, up to 3 tiles)
13. Avatar picker
14. Settings (max 5 rows: theme, color, text size, sounds, read aloud)
15. Streak sheet
16. Daily mission sheet
17. Time's up screen

Routes: `/profiles`, `/home`, `/lesson/{lesson}`, `/rewards`, `/me`, `/me/avatar`, `/me/settings`, `/login`, `/register`, `/verify`, `/forgot-password`, `/onboarding`, `/parent/...`, `/admin`.

Navigation rules: max 2 taps from Home to starting a lesson; every sheet closes by tap outside, ✕ or main button; no dead ends; use `wire:navigate`; skeletons instead of spinners.

**Self-check after each screen:** screenshot at 360×740 in light and dark, count primary buttons / visible numbers / text lines / blocks against the table, simplify if over the limit.

---

## 11. Paid plan readiness (build now, no payments in release 1)

**Release 1 is free for everyone.** Prepare the structure so a paid plan can be added without rewrites:

- Create the billing tables of 4.5 and a `PlanSeeder` with `free` and `premium` rows (prices empty or 0).
- Keep `courses.is_premium` and `children` limits (max children per parent) as data.
- Add **one central place for feature checks**, e.g. a `Gate::define('premium', ...)` or small `Entitlements` service that answers "can this parent use X?". **In release 1 it always returns true.** All premium-related UI and code must go through it, never through scattered `if` checks.
- Planned split (not enforced yet): **Free** = grade path for all subjects, 1 child, limited themes. **Premium** = all themes, up to 4 children, full reports and weekly email, offline packs, extra avatars.
- Any lock card is shown to the **parent only**, never as a sales pitch to a child.
- **Do not build:** payment gateway, checkout, webhooks, invoices, or any provider SDK. A `PaymentGateway` interface may be added later (Georgian providers such as BOG/TBC, or Stripe; prices in GEL).

---

## 12. Admin panel (Filament, `/admin`, `users.is_admin`)

- **Resources:** Grades, Subjects, Courses (nested Units → Lessons), Exercises, Avatars, Achievements, Plans, Users (read-mostly), Children (read-only).
- **Exercise form:** choose `type`, then fields come from `ExerciseType::filamentSchema()`; validate with `rules()`.
- **Preview** action: render the exercise in a phone-sized frame with the real Blade component.
- **Bulk import** of exercises from JSON/CSV for a lesson.
- **Audio:** manual upload (text-to-speech action later).

---

## 13. Build phases

**Phase 0: Setup.** Livewire, Tailwind v4, Alpine, Pest (and Filament after approval). Tokens in `resources/css/app.css`. Georgian font `ALK Life` from `public/assets/fonts/` (section 2), glyph test, Noto Sans Georgian fallback. Base layout (`max-w-[480px]`, safe-area padding, dark mode). `lang/ka`. Timezone `Asia/Tbilisi`, locale `ka`.

**Phase 1: Accounts.** Parent register/login/OTP/reset, PIN, child profiles, "Who's playing?", `EnsureChildSelected`, username generator, avatar picker, onboarding (grade → avatar → username).

**Phase 2: Content model + admin.** Migrations/models (4.2, 4.5 tables), Filament resources, `ExerciseRegistry` with all 11 type classes (`rules()` and `check()` fully tested), seeders from section 6.

**Phase 3: Lesson player + first 6 types.** `LessonPlayer`, shell, feedback sheet, sounds, re-queue, result screen, attempts saved. Types: `choice`, `match_pairs`, `build_word`, `fill_gap`, `number_input`, `true_false`.

**Phase 4: Path and progress.** Home path, subject picker, themes, lock/unlock, stars, resume, start/locked sheets.

**Phase 5: Remaining types.** `riddle`, `categorize`, `order`, `hotspot`, `word_search`.

**Phase 6: Gamification.** XP/levels, streak and freezes, achievements and badge modal, avatar unlocks, daily mission, league and weekly rollover command.

**Phase 7: Parent zone and settings.** PIN gate, reports and chart, weekly email, screen time, bedtime lock, break reminder, child settings.

**Phase 8: PWA and polish.** Manifest (Georgian `lang: ka`, real icons), service worker offline cache, install prompt (shown to the parent), accessibility and performance pass, entitlements check wired everywhere (section 11).

Estimated time: about **12-14 weeks** for one developer with AI help (content writing and Georgian audio/review excluded). Playable MVP after Phase 4 is about 7 weeks.

---

## 14. Quality checklist

- [ ] Every `ExerciseType::check()` has Pest tests for correct, wrong and malformed answers.
- [ ] Feature test: register → OTP → create child → complete a lesson → XP, streak and achievement updated.
- [ ] Streak tests across midnight in Asia/Tbilisi and with freezes.
- [ ] League rollover test (promote/stay/demote, grade bands).
- [ ] `migrate:fresh --seed` produces a playable app with all 11 game types.
- [ ] Adding a grade-5 course in the admin makes it appear for a grade-5 child with no code change.
- [ ] No hard-coded Georgian strings in Blade (all in `lang/`).
- [ ] No `uppercase` class on Georgian text.
- [ ] All premium checks go through the single entitlements point; no payment code exists.
- [ ] Every page checked at 360px, light and dark.
- [ ] No N+1 queries (`Model::preventLazyLoading()` in dev).
- [ ] Rate limiting on auth, OTP and answer submission.
- [ ] Lighthouse mobile: Performance ≥ 85, Accessibility ≥ 95, PWA installable.

---

## 15. Open decisions for the product owner

1. Font licence check (commercial use) for the chosen typeface.ge fonts.
2. Native-speaker review of the dummy Georgian content.
3. Text-to-speech provider or human recordings for Georgian audio.
4. Real curriculum mapping for grades 1-4 (subjects and topics per grade).
5. Premium feature split and prices (before payments are built).
6. Who authors the real content (teachers, or AI-drafted and teacher-reviewed).
