import type {Actor} from '@/Types/Actor'

export interface WorkflowRun {
  id: number
  date: number
  organization: string
  repository: string
  repository_id: number
  minutes: number
  price_per_minute: number
  total_price: number
  workflow: string
  os: string
  actor: Actor
}
