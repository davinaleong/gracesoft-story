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
* [x] Human-friendly labels:

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
* [x] Provider buttons:

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

* [x] Loading (connecting)
* [x] Success (connected)
* [x] Error (failed connection)

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
* [x] Multi-select (future)
* [x] Inline label editing

---

## States

* [x] Loading (skeleton list)
* [x] Empty (no commits)
* [x] Error state

---

---

# 📄 4. CHAPTER DETAILS (INSPECTOR / MODAL)

## Content

* [x] Full commit message
* [x] Author + email
* [x] Date/time
* [x] Labels
* [x] Changes (optional MVP skip)

---

## Layout

* [x] Right-side panel OR modal
* [x] Scrollable content

---

## Actions

* [x] Add/remove labels
* [x] Close panel

---

---

# 🏷️ 5. LABEL MANAGEMENT PAGE

## Content

* [x] List of labels
* [x] Create new label
* [x] Edit label
* [x] Delete label

---

## UI Elements

* [x] Color picker
* [x] Name input
* [x] Label preview

---

## Design

* [x] Label chips (rounded)
* [x] Color-coded system
* [x] Inline editing preferred

---

## States

* [x] No labels yet
* [x] Editing state
* [x] Delete confirmation

---

---

# 🏷️ 6. LABEL TAGGING (INLINE UI)

## Placement

* [x] Inside timeline item
* [x] Inside chapter details

---

## Interaction

* [x] “+ Add Label” button
* [x] Dropdown / multi-select
* [x] Instant apply/remove

---

## UX Rules

* [x] Fast (no page reload)
* [x] Optional (not forced)
* [x] Minimal clicks

---

---

# 📊 7. INSIGHTS DASHBOARD (PAID)

## Layout

* [x] Page title
* [x] Summary cards (top)
* [x] Charts (middle)
* [x] Insights text (bottom)

---

## Components

### Summary Cards

* [x] Total commits
* [x] Top label
* [x] Weekly activity

---

### Charts

* [x] Pie chart (label distribution)
* [x] Bar chart (activity over time)

---

### Insights

* [x] Human-readable summaries

  * “You spent 40% on Bug Fixes”
  * “Feature work increased this week”

---

## States

* [x] Loading (skeleton cards)
* [x] Empty (no data)
* [x] Locked (paywall preview)

---

---

# 🔐 8. PAYWALL / LOCKED UI

## Design

* [x] Blurred or faded content
* [x] Visible preview of features

---

## Content

* [x] Short explanation
* [x] CTA button:
  → “Unlock insights”

---

## UX Rules

* [x] No aggressive popups
* [x] Show value first

---

---

# ⚙️ 9. SETTINGS PAGE

## Sections

* [x] Connected accounts
* [x] Manage repositories
* [x] Subscription / billing

---

## UI Elements

* [x] Disconnect provider button
* [x] Sync status
* [x] Plan details

---

---

# ⚡ 10. LOADING & STATES

## Skeleton Loaders

* [x] Timeline skeleton
* [x] Dashboard skeleton
* [x] Sidebar loading

---

## Empty States

* [x] No repo → “Connect your repo”
* [x] No commits → “No chapters yet”
* [x] No labels → “Add labels to understand your work”

---

## Error States

* [x] API failure message
* [x] Retry button

---

---

# 🎯 11. PERFORMANCE UX

* [x] Pagination / infinite scroll
* [x] Lazy load commits
* [x] Optimistic UI for tagging

---

---

# 🧠 FINAL DESIGN RULE

* [x] If it feels like a **developer tool** → simplify
* [x] If it feels like a **story** → you’re on track
