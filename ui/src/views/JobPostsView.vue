<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'
import dayjs from 'dayjs'
import { useUiStore } from '@/stores/ui'
import { useAuthStore } from '@/stores/auth'
import { BankOutlined, CalendarOutlined, EnvironmentOutlined } from '@ant-design/icons-vue'

const route = useRoute()
const router = useRouter()
const uiStore = useUiStore()
const authStore = useAuthStore()

const loading = ref<boolean>(true)
const page = ref<number>(1)
const jobsResponse = ref<any>([])
const jobs = ref<any[]>([])

function updateUrlParams() {
  const query = { ...route.query }

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
  }
  let response = await api.get('/api/jobs/posts', { params })
  if (response.status === 200) {
    jobsResponse.value = response.data
    jobs.value = jobsResponse.value.data.data
  } else {
    console.error('Error fetching jobs:', response)
  }
  loading.value = false
}

// Check if there are query parameters
page.value = parseInt(useRoute().query.page as string) || 1

// Fetch the jobs when the component is mounted. First page by default.
fetchJobs()

onMounted(() => {
  fetchJobs()
})
</script>
<template>
  <main class="px-3">
    <a-typography>
      <a-typography-title>Jobs Posts</a-typography-title>
      <a-typography-paragraph v-if="!authStore.user?.is_moderator">
        You can find job posts you own here.
      </a-typography-paragraph>
      <a-typography-paragraph v-else>
        You can find a list of all job posts in the board here.
      </a-typography-paragraph>
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
            <div class="mb-2" v-if="authStore.isAuthenticated">
              <a-tag color="yellow" v-if="job.spam_level == -1">Under Review</a-tag>
              <a-tag color="red" v-if="job.spam_level == 1">Marked as spam</a-tag>
              <a-tag color="green" v-if="job.spam_level == 0">Active</a-tag>
            </div>
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
