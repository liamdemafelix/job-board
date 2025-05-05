<script setup lang="ts">
import { onMounted, ref } from 'vue'
import dayjs from 'dayjs'
import api from '@/api'
import { useRoute } from 'vue-router'
import { BankOutlined, CalendarOutlined, EnvironmentOutlined, FrownOutlined,  } from '@ant-design/icons-vue'

const loading = ref<boolean>(true)
const notFound = ref<boolean>(false)
const job = ref<any>({})

const route = useRoute()
async function fetchJob() {
  loading.value = true
  const jobId = route.params.id
  try {
    let response = await api.get(`/api/jobs/${jobId}`)
    job.value = response.data.data
  } catch (error) {
    notFound.value = true
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchJob()
})
</script>
<template>
  <div class="text-xs-center" v-if="loading">
    <a-spin :loading="loading" />
  </div>
  <div v-else-if="notFound">
    <a-empty>
      <template #image>
        <frown-outlined :style="{ fontSize: '100px' }" />
      </template>
      <template #description>
        <h1>Sorry!</h1>
        <p>We can't find the job you were looking for.</p>
        <a-button type="primary" @click="$router.push({ name: 'home' })">
          Go Back
        </a-button>
      </template>
    </a-empty>
  </div>
  <div v-else>
    <div class="px-5">
      <h1>{{ job.name }}</h1>
      <a-space direction="vertical">
        <div><environment-outlined /> {{ job.office }}</div>
        <div><bank-outlined /> {{ job.user?.company ?? job.subcompany }}</div>
        <div><calendar-outlined /> {{ dayjs(job.created_at).format('MMMM D, YYYY hh:mm A') }}</div>
        <div class="mt-2" v-if="job.keywords.length > 0">
          <template v-for="(keyword) in job.keywords">
            <a-tag class="mt-2" color="blue">
              {{ keyword.name }}
            </a-tag>
          </template>
        </div>
      </a-space>
      <div v-if="job.job_post_descriptions.length > 0" class="mt-4">
        <a-divider />
        <template v-for="(description, index) in job.job_post_descriptions">
          <div class="mt-2">
            <div style="font-weight: bold;" v-html="description.name" />
            <div v-html="description.value" />
          </div>
        </template>
      </div>
      <div v-else class="mt-4">
        <a-divider />
        <em>The employer has not provided a description for this job.</em>
      </div>
    </div>
  </div>
</template>
