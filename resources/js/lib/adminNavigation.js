import {
  Bell,
  Beef,
  CalendarDays,
  Coins,
  FileText,
  Gift,
  HandCoins,
  Home,
  Megaphone,
  LayoutDashboard,
  Package,
  PiggyBank,
  Scissors,
  ShieldCheck,
  UserCog,
  Users,
  Wallet,
  ChevronDown,
  CreditCard,
  List,
  UserPlus,
  BookOpen,
  Archive,
  Settings,
} from "lucide-vue-next"

export const adminNavigation = [
  {
    label: "Dashboard",
    route: "dashboard",
    icon: LayoutDashboard,
    match: ["dashboard"],
    group: "Ikhtisar",
    permissions: ["dashboard.view"],
  },
  {
    label: "Keuangan",
    route: "finance.index",
    icon: Wallet,
    match: ["finance.*"],
    group: "Manajemen",
    roles: ["admin", "super-admin", "ketua-dkm", "bendahara"],
    permissions: ["finance.view"],
    children: [
      { label: "Transaksi", route: "finance.index", icon: List, match: ["finance.index"] },
      { label: "Kategori", route: "finance-categories.index", icon: BookOpen, match: ["finance-categories.*"] },
    ],
  },
  {
    label: "Donasi",
    route: "donations.index",
    icon: HandCoins,
    match: ["donations.*"],
    group: "Manajemen",
    roles: ["admin", "super-admin", "ketua-dkm", "bendahara"],
    permissions: ["donation.view"],
    children: [
      { label: "Daftar Donasi", route: "donations.index", icon: List, match: ["donations.index"] },
      { label: "Kategori", route: "donation-categories.index", icon: Archive, match: ["donation-categories.*"] },
      { label: "Donatur", route: "donors.index", icon: UserPlus, match: ["donors.*"] },
    ],
  },
  {
    label: "Jadwal",
    route: "schedules.index",
    icon: CalendarDays,
    match: ["schedules.*"],
    group: "Manajemen",
    roles: ["admin", "super-admin", "ketua-dkm", "sekretaris", "marbot", "panitia"],
    permissions: ["schedule.view"],
    children: [
      { label: "Jadwal Shalat", route: "schedules.index", icon: CalendarDays, match: ["schedules.index"], tab: "prayer" },
      { label: "Jadwal Petugas", route: "schedules.index", icon: UserCog, match: ["schedules.index"], tab: "service" },
    ],
  },
  {
    label: "Kegiatan",
    route: "events.index",
    icon: Home,
    match: ["events.*"],
    group: "Manajemen",
    roles: ["admin", "super-admin", "ketua-dkm", "sekretaris"],
    permissions: ["event.view"],
  },
  {
    label: "Jamaah",
    route: "jamaahs.index",
    icon: Users,
    match: ["jamaahs.*"],
    group: "Manajemen",
    roles: ["admin", "super-admin", "ketua-dkm", "sekretaris"],
    permissions: ["jamaah.view"],
  },
  {
    label: "Inventaris",
    route: "assets.index",
    icon: Package,
    match: ["assets.*"],
    group: "Manajemen",
    roles: ["admin", "super-admin", "ketua-dkm", "marbot"],
    permissions: ["asset.view"],
  },
  {
    label: "Dokumen",
    route: "documents.index",
    icon: FileText,
    match: ["documents.*"],
    group: "Manajemen",
    roles: ["admin", "super-admin", "ketua-dkm", "sekretaris"],
    permissions: ["document.view"],
  },
  {
    label: "Pengumuman",
    route: "announcements.index",
    icon: Megaphone,
    match: ["announcements.*"],
    group: "Manajemen",
    roles: ["admin", "super-admin", "ketua-dkm", "sekretaris"],
    permissions: ["announcement.view"],
  },
  {
    label: "Laporan",
    route: "reports.index",
    icon: Coins,
    match: ["reports.*"],
    group: "Manajemen",
    roles: ["admin", "super-admin", "ketua-dkm", "sekretaris", "bendahara"],
    permissions: ["report.view", "reports.view"],
  },
  {
    label: "Pengaturan",
    route: "settings.index",
    icon: Settings,
    match: ["settings.*"],
    group: "Manajemen",
    roles: ["admin", "super-admin", "ketua-dkm", "sekretaris"],
    permissions: ["setting.view", "setting.manage"],
  },
  {
    label: "Qurban",
    route: "qurban.group",
    icon: Beef,
    match: ["qurban.*", "savings.*", "animals.*", "participants.*", "slaughterings.*", "volunteers.*", "distributions.*"],
    group: "",
    roles: ["admin", "super-admin", "panitia"],
    permissions: ["qurban.view"],
    children: [
      { label: "Dashboard", route: "qurban.dashboard", icon: LayoutDashboard, match: ["qurban.dashboard"] },
      { label: "Tabungan", route: "savings.index", icon: PiggyBank, match: ["savings.*"] },
      { label: "Hewan", route: "animals.index", icon: Beef, match: ["animals.*"] },
      { label: "Peserta", route: "participants.index", icon: Users, match: ["participants.*"] },
      { label: "Penyembelihan", route: "slaughterings.index", icon: Scissors, match: ["slaughterings.*"] },
      { label: "Relawan", route: "volunteers.index", icon: UserCog, match: ["volunteers.*"] },
      { label: "Distribusi", route: "distributions.index", icon: Gift, match: ["distributions.*"] },
    ],
  },
  {
    label: "Profil",
    route: "profile.edit",
    icon: ShieldCheck,
    match: ["profile.*"],
    group: "Akun",
    hideFromSidebar: true,
  },
]

const normalizeRole = (value) => String(value ?? "").trim().toLowerCase()
const normalizePermission = (value) => String(value ?? "").trim().toLowerCase()

export const resolveUserRoles = (auth) => {
  const roles = new Set()
  const user = auth?.user ?? auth

  if (Array.isArray(auth?.role_slugs)) {
    auth.role_slugs.forEach((role) => roles.add(normalizeRole(role)))
  }
  if (user?.role) roles.add(normalizeRole(user.role))

  if (Array.isArray(user?.roles)) {
    user.roles.forEach((role) => {
      if (typeof role === "string") roles.add(normalizeRole(role))
      else if (role?.slug) roles.add(normalizeRole(role.slug))
      else if (role?.name) roles.add(normalizeRole(role.name))
    })
  }

  return Array.from(roles).filter(Boolean)
}

export const resolveUserPermissions = (auth) => {
  const permissions = new Set()
  const user = auth?.user ?? auth

  if (Array.isArray(auth?.permission_slugs)) {
    auth.permission_slugs.forEach((permission) => permissions.add(normalizePermission(permission)))
  }

  if (Array.isArray(user?.permission_slugs)) {
    user.permission_slugs.forEach((permission) => permissions.add(normalizePermission(permission)))
  }

  return Array.from(permissions).filter(Boolean)
}

export const canAccessNavItem = (item, auth) => {
  if (Array.isArray(item.permissions) && item.permissions.length > 0) {
    const userPermissions = resolveUserPermissions(auth)
    if (userPermissions.length > 0) {
      return item.permissions.some((permission) => userPermissions.includes(normalizePermission(permission)))
    }
  }

  if (!Array.isArray(item.roles) || item.roles.length === 0) return true

  const userRoles = resolveUserRoles(auth)
  if (userRoles.length === 0) return false

  return item.roles.some((role) => userRoles.includes(normalizeRole(role)))
}

export const getVisibleNavigation = (auth) =>
  adminNavigation.filter((item) => canAccessNavItem(item, auth) && !item.hideFromSidebar)

export const flattenNavigation = (items = adminNavigation) =>
  items.flatMap((item) => [item, ...(item.children ? flattenNavigation(item.children) : [])])

export const isNavItemActive = (item, currentRouteName, currentPathname = "") => {
  if (!item) return false
  if (item.route === currentRouteName) return true

  if (Array.isArray(item.match) && currentRouteName) {
    return item.match.some((pattern) => {
      if (pattern.endsWith(".*")) {
        return currentRouteName.startsWith(pattern.slice(0, -1))
      }

      return currentRouteName === pattern
    })
  }

  return currentPathname === item.href
}

export const isNavItemOrChildActive = (item, currentRouteName, currentPathname = "") => {
  if (isNavItemActive(item, currentRouteName, currentPathname)) return true
  if (item.children) {
    return item.children.some((child) => isNavItemActive(child, currentRouteName, currentPathname))
  }
  return false
}

export const findCurrentNavItem = (auth, currentRouteName, currentPathname = "") => {
  const allItems = flattenNavigation(
    adminNavigation.filter((item) => canAccessNavItem(item, auth))
  )
  return allItems.find((item) => isNavItemActive(item, currentRouteName, currentPathname))
}

export const findParentNavItem = (auth, currentRouteName, currentPathname = "") => {
  const visible = adminNavigation.filter((item) => canAccessNavItem(item, auth) && !item.hideFromSidebar)
  return visible.find((item) =>
    item.children?.some((child) => isNavItemActive(child, currentRouteName, currentPathname))
  )
}

export const buildBreadcrumbs = ({ currentItem, title, parentItem }) => {
  const breadcrumbs = [{ label: "Admin", current: false }]

  if (parentItem?.group && parentItem.group !== "Ikhtisar") {
    breadcrumbs.push({ label: parentItem.group, current: false })
  }

  if (currentItem?.group && currentItem.group !== "Ikhtisar" && currentItem.group !== parentItem?.group) {
    breadcrumbs.push({ label: currentItem.group, current: false })
  }

  if (parentItem && currentItem !== parentItem) {
    breadcrumbs.push({ label: parentItem.label, current: false })
  }

  if (title) {
    breadcrumbs.push({ label: title, current: true })
  } else if (currentItem?.label) {
    breadcrumbs.push({ label: currentItem.label, current: true })
  }

  return breadcrumbs
}
