# 🍎 GraceSoft Story — UI Page Design Checklist

---

# Design Inspiration

* Pre-glass MacOS UI
* Black
* White
* Tailwind Gray spectrum
* Tailwind Sky as accent

---

# 🧭 GLOBAL (APPLIES TO ALL PAGES)

## Layout

* [ ] Sidebar (fixed, left)
* [ ] Main content area (center)
* [ ] Optional inspector panel (right)

---

## Visual Style

* [ ] Light background (white / gray-50)
* [ ] Subtle borders (`gray-200`)
* [ ] Soft shadows (`shadow-sm`)
* [ ] Rounded corners (`rounded-lg`)

---

## Typography

* [ ] Clear hierarchy (title > body > meta)
* [ ] Muted metadata text (gray-500)
* [ ] Consistent spacing

---

## Interaction

* [ ] Hover states (subtle background change)
* [ ] Smooth transitions (150–200ms)
* [ ] Click feedback (highlight / active state)

---

## Copy

* [ ] No Git jargon
* [ ] Human-friendly labels:

  * commit → chapter
  * branch → path
  * diff → changes

---

---

# 🧱 1. SIDEBAR (GLOBAL COMPONENT)

## Structure

* [ ] Logo / product name
* [ ] Repositories list
* [ ] Insights (paid)
* [ ] Settings

---

## Design

* [ ] Light gray background
* [ ] Active item highlight
* [ ] Icons + labels
* [ ] Scrollable repo list

---

## States

* [ ] Empty state (no repos)
* [ ] Active repo selected
* [ ] Hover state

---

---

# 🔌 2. CONNECT REPOSITORY PAGE

## Content

* [ ] Title: “Connect your repository”
* [ ] Provider buttons:

  * [ ] GitHub
  * [ ] GitLab (disabled / coming soon)
  * [ ] Bitbucket (disabled / coming soon)

---

## UI Elements

* [ ] OAuth button (primary CTA)
* [ ] Provider logos
* [ ] Short explanation text

---

## States

* [ ] Loading (connecting)
* [ ] Success (connected)
* [ ] Error (failed connection)

---

## Empty State Message

* [ ] “Start your story by connecting a repository”

---

---

# 📖 3. STORY TIMELINE PAGE (CORE)

## Layout

* [ ] Page title (repo name)
* [ ] Timeline list (vertical)
* [ ] Optional filters (top)

---

## Timeline Item (Chapter)

### Content

* [ ] Commit message (title)
* [ ] Author
* [ ] Date/time
* [ ] Labels (badges)

---

### Design

* [ ] Clean list (NOT heavy cards)
* [ ] Subtle separators
* [ ] Highlight on hover

---

### Interaction

* [ ] Click → open chapter details
* [ ] Multi-select (future)
* [ ] Inline label editing

---

## States

* [ ] Loading (skeleton list)
* [ ] Empty (no commits)
* [ ] Error state

---

---

# 📄 4. CHAPTER DETAILS (INSPECTOR / MODAL)

## Content

* [ ] Full commit message
* [ ] Author + email
* [ ] Date/time
* [ ] Labels
* [ ] Changes (optional MVP skip)

---

## Layout

* [ ] Right-side panel OR modal
* [ ] Scrollable content

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
