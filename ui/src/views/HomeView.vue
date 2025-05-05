<script setup lang="ts">
/**
 * Data Parsing and Display: Parse the data from the external source and display it in a user-friendly format on the job board. Each listing should at least include the job title,  description, and a link to the full job posting.
 */
import { onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUiStore } from '@/stores/ui'
import { BankOutlined, CalendarOutlined, EnvironmentOutlined } from '@ant-design/icons-vue'
import api from '@/api'
import dayjs from 'dayjs'

const route = useRoute()
const router = useRouter()
const uiStore = useUiStore()
const jobsResponse = ref<any>([])
const jobs = ref<any>([])
const loading = ref<boolean>(true)
const page = ref<number>(1)
const tags = ref<string>('')

uiStore.$subscribe((mutation, state) => {
  page.value = 1 // Reset page to 1 when keywords change
  updateUrlParams()
})

function updateUrlParams() {
  const query = { ...route.query }
  if (uiStore.activeKeywords.length > 0) {
    query.tags = uiStore.activeKeywords.join(',')
  } else {
    delete query.tags
  }

  if (page.value > 1) {
    query.page = page.value.toString()
  } else {
    delete query.page
  }
  router.push({query})
}

async function fetchJobs(pageNum?: number) {
  loading.value = true
  if (pageNum !== undefined) {
    page.value = pageNum
  }
  updateUrlParams()
  let params: {
    page: number
    tags?: string
  } = {
    page: page.value,
    tags: tags.value,
  }
  let response = await api.get('/api/jobs', { params })
  if (response.status === 200) {
    jobsResponse.value = response.data.data
    jobs.value = jobsResponse.value.data
  } else {
    console.error('Error fetching jobs:', response)
  }
  loading.value = false
}

// Check if there are query parameters
tags.value = useRoute().query.tags as string
if (tags.value) {
  uiStore.activeKeywords = tags.value.split(',')
}
page.value = parseInt(useRoute().query.page as string) || 1

// Fetch the jobs when the component is mounted. First page by default.
fetchJobs()

// Watch for changes in the query params
watch(
  () => route.query,
  (newParams) => {
    tags.value = newParams.tags as string
    page.value = parseInt(newParams.page as string) || 1
    fetchJobs()
  },
)
</script>
<template>
  <main class="px-3">
    <a-typography>
      <a-typography-title>Available Jobs</a-typography-title>
    </a-typography>
    <a-divider />
    <div class="text-xs-center" v-if="loading">
      <a-spin :loading="loading" />
    </div>
    <div v-else>
      <a-row :gutter="[16, 16]">
        <a-col :span="24" v-for="job in jobs">
          <a-card :key="job.id">
            <h1>{{ job.name }}</h1>
            <a-space direction="vertical">
              <div><environment-outlined /> {{ job.office }}</div>
              <div><bank-outlined /> {{ job.user?.company ?? job.subcompany }}</div>
              <div><calendar-outlined /> {{ dayjs(job.created_at).format('MMMM D, YYYY hh:mm A') }}</div>
              <div class="mt-2" v-if="job.keywords.length > 0">
                <template v-for="(keyword) in job.keywords">
                  <router-link :to="{ name: 'home', query: { tags: keyword.name } }" @click="uiStore.activeKeywords = [keyword.name]">
                    <a-tag class="mt-2" color="blue">
                      {{ keyword.name }}
                    </a-tag>
                  </router-link>
                </template>
              </div>
            </a-space>
            <div v-if="job.job_post_descriptions.length > 0" class="mt-4">
              <a-divider />
              <div v-html="job.job_post_descriptions[0].name" style="font-weight: bold;" />
              <div v-html="job.job_post_descriptions[0].value.length > 200 ?
              job.job_post_descriptions[0].value.substring(0, 200) + '...' :
              job.job_post_descriptions[0].value" />
            </div>
            <div v-else class="mt-4">
              <a-divider />
              <em>The employer has not provided a description for this job.</em>
            </div>
            <div class="mt-4 text-xs-right">
              <a-button type="primary" @click="$router.push({ name: 'job', params: { id: job.id } })">
                View More
              </a-button>
            </div>
          </a-card>
        </a-col>
      </a-row>
      <a-row :gutter="[16, 16]" class="mt-4">
        <a-col :span="24" class="text-xs-right">
          <a-pagination
            :total="jobsResponse.total"
            :page-size="jobsResponse.per_page"
            :current="page"
            @change="fetchJobs"
          />
        </a-col>
      </a-row>
    </div>
  </main>
</template>
