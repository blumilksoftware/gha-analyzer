import { type Ref, computed, ref, watch } from 'vue'

interface Sortable<T> {
  items: T[]
  sort: keyof T
  order: 'asc' | 'desc'
}

export const withSort = <T>(items: T[], sortBy: keyof T) => {
  const data = ref({
    items,
    sort: sortBy,
    order: 'desc',
  }) as Ref<Sortable<T>>

  watch (items, () => {
    data.value.items = items
  })

  const sorted = computed(() => {
    let items = [...data.value.items]

    if (data.value.sort) {
      items = items.sort((a, b) => {
        const key = data.value.sort

        const valueA = a[key]
        const valueB = b[key]

        if (typeof valueA === 'number' && typeof valueB === 'number') {
          return valueA - valueB
        }

        if (typeof valueA === 'string' && typeof valueB === 'string') {
          return valueA.localeCompare(valueB)
        }

        return 0
      })
    }

    if (data.value.order === 'desc') {
      items.reverse()
    }

    return items
  })

  const filterBy = (tag: keyof T) => {
    if (data.value.sort === tag) {
      data.value.order = data.value.order === 'desc' ? 'asc' : 'desc'
    }
    else {
      data.value.sort = tag
      data.value.order = 'desc'
    }
  }

  return { sorted, data, filterBy }
}
