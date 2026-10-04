export const toArray = (value) => (Array.isArray(value) ? value : [])

export const toNumber = (value, fallback = 0) => {
  const parsed = Number(value)
  return Number.isFinite(parsed) ? parsed : fallback
}

export const normalizePaginated = (value) => {
  const data = toArray(value?.data)

  return {
    ...value,
    data,
    total: toNumber(value?.total, data.length),
    prev_page_url: value?.prev_page_url ?? null,
    next_page_url: value?.next_page_url ?? null,
  }
}

export const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(toNumber(value))

export const formatNumber = (value) => new Intl.NumberFormat("id-ID").format(toNumber(value))

export const formatDate = (value, fallback = "-") => {
  if (!value) return fallback

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return fallback

  return new Intl.DateTimeFormat("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  }).format(date)
}

export const formatDateTime = (value, fallback = "-") => {
  if (!value) return fallback

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return fallback

  return new Intl.DateTimeFormat("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  }).format(date)
}

export const hasNamedRoute = (name) => {
  try {
    return typeof route === "function" && typeof route().has === "function" && route().has(name)
  } catch {
    return false
  }
}

export const resolveRoute = (name, ...params) => {
  if (!hasNamedRoute(name)) return null

  try {
    return route(name, ...params)
  } catch {
    return null
  }
}
