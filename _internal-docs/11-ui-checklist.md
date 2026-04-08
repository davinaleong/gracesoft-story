# 🍎 GraceSoft Story — UI Page Design Checklist

---

# Design Inspiration

* ~~Pre-glass MacOS UI~~ iPhone Notes app
* Black
* White
* Tailwind Gray spectrum
* Tailwind Sky as accent
* Lucide icons

---

# 🧭 GLOBAL (APPLIES TO ALL PAGES)

## Layout

* [x] Sidebar (fixed, left)
* [x] Main content area (center)
* [x] Optional inspector panel (right)

---

## Visual Style

* [x] Light background (white / gray-50)
* [x] Subtle borders (`gray-200`)
* [x] Soft shadows (`shadow-sm`)
* [x] Rounded corners (`rounded-lg`)

---

## Typography

* [x] Clear hierarchy (title > body > meta)
* [x] Muted metadata text (gray-500)
* [x] Consistent spacing

---

## Interaction

* [x] Hover states (subtle background change)
* [x] Smooth transitions (150–200ms)
* [x] Click feedback (highlight / active state)

---

## Copy

* [x] No Git jargon
* [ ] Human-friendly labels:

  * commit → chapter
  * branch → path
  * diff → changes

---

---

# 🧱 1. SIDEBAR (GLOBAL COMPONENT)

## Structure

* [x] Logo / product name
* [x] Repositories list
* [x] Insights (paid)
* [x] Settings

---

## Design

* [x] Light gray background
* [x] Active item highlight
* [x] Icons + labels
* [x] Scrollable repo list

---

## States

* [x] Empty state (no repos)
* [x] Active repo selected
* [x] Hover state

---

---

# 🔌 2. CONNECT REPOSITORY PAGE

## Content

* [x] Title: “Connect your repository”
* [ ] Provider buttons:

  * [x] GitHub
  * [x] GitLab (disabled / coming soon)
  * [x] Bitbucket (disabled / coming soon)

---

## UI Elements

* [x] OAuth button (primary CTA)
* [x] Provider logos
* [x] Short explanation text

---

## States

* [ ] Loading (connecting)
* [x] Success (connected)
* [ ] Error (failed connection)

---

## Empty State Message

* [x] “Start your story by connecting a repository”

---

---

# 📖 3. STORY TIMELINE PAGE (CORE)

## Layout

* [x] Page title (repo name)
* [x] Timeline list (vertical)
* [x] Optional filters (top)

---

## Timeline Item (Chapter)

### Content

* [x] Commit message (title)
* [x] Author
* [x] Date/time
* [x] Labels (badges)

---

### Design

* [x] Clean list (NOT heavy cards)
* [x] Subtle separators
* [x] Highlight on hover

---

### Interaction

* [x] Click → open chapter details
* [ ] Multi-select (future)
* [ ] Inline label editing

---

## States

* [ ] Loading (skeleton list)
* [x] Empty (no commits)
* [ ] Error state

---

---

# 📄 4. CHAPTER DETAILS (INSPECTOR / MODAL)

## Content

* [x] Full commit message
* [x] Author + email
* [x] Date/time
* [x] Labels
* [ ] Changes (optional MVP skip)

---

## Layout

* [x] Right-side panel OR modal
* [x] Scrollable content

---

## Actions

* [ ] Add/remove labels
* [ ] Close panel

---

---

# 🏷️ 5. LABEL MANAGEMENT PAGE

## Content

* [ ] List of labels
* [ ] Create new label
* [ ] Edit label
* [ ] Delete label

---

## UI Elements

* [ ] Color picker
* [ ] Name input
* [ ] Label preview

---

## Design

* [ ] Label chips (rounded)
* [ ] Color-coded system
* [ ] Inline editing preferred

---

## States

* [ ] No labels yet
* [ ] Editing state
* [ ] Delete confirmation

---

---

# 🏷️ 6. LABEL TAGGING (INLINE UI)

## Placement

* [ ] Inside timeline item
* [ ] Inside chapter details

---

## Interaction

* [ ] “+ Add Label” button
* [ ] Dropdown / multi-select
* [ ] Instant apply/remove

---

## UX Rules

* [ ] Fast (no page reload)
* [ ] Optional (not forced)
* [ ] Minimal clicks

---

---

# 📊 7. INSIGHTS DASHBOARD (PAID)

## Layout

* [ ] Page title
* [ ] Summary cards (top)
* [ ] Charts (middle)
* [ ] Insights text (bottom)

---

## Components

### Summary Cards

* [ ] Total commits
* [ ] Top label
* [ ] Weekly activity

---

### Charts

* [ ] Pie chart (label distribution)
* [ ] Bar chart (activity over time)

---

### Insights

* [ ] Human-readable summaries

  * “You spent 40% on Bug Fixes”
  * “Feature work increased this week”

---

## States

* [ ] Loading (skeleton cards)
* [ ] Empty (no data)
* [ ] Locked (paywall preview)

---

---

# 🔐 8. PAYWALL / LOCKED UI

## Design

* [ ] Blurred or faded content
* [ ] Visible preview of features

---

## Content

* [ ] Short explanation
* [ ] CTA button:
  → “Unlock insights”

---

## UX Rules

* [ ] No aggressive popups
* [ ] Show value first

---

---

# ⚙️ 9. SETTINGS PAGE

## Sections

* [ ] Connected accounts
* [ ] Manage repositories
* [ ] Subscription / billing

---

## UI Elements

* [ ] Disconnect provider button
* [ ] Sync status
* [ ] Plan details

---

---

# ⚡ 10. LOADING & STATES

## Skeleton Loaders

* [ ] Timeline skeleton
* [ ] Dashboard skeleton
* [ ] Sidebar loading

---

## Empty States

* [ ] No repo → “Connect your repo”
* [ ] No commits → “No chapters yet”
* [ ] No labels → “Add labels to understand your work”

---

## Error States

* [ ] API failure message
* [ ] Retry button

---

---

# 🎯 11. PERFORMANCE UX

* [ ] Pagination / infinite scroll
* [ ] Lazy load commits
* [ ] Optimistic UI for tagging

---

---

# 🧠 FINAL DESIGN RULE

* [ ] If it feels like a **developer tool** → simplify
* [ ] If it feels like a **story** → you’re on track
