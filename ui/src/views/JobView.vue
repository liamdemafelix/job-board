<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import dayjs from 'dayjs'
import api from '@/api'
import { useRoute } from 'vue-router'
import { BankOutlined, CalendarOutlined, EnvironmentOutlined, FrownOutlined, } from '@ant-design/icons-vue'
import { notification } from 'ant-design-vue'

const loading = ref<boolean>(true)
const modLoading = ref<string>('')
const notFound = ref<boolean>(false)
const job = ref<any>({})

const authStore = useAuthStore()
const uiStore = useUiStore()
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

async function moderate(action: string) {
  modLoading.value = action
  try {
    let spam_level = -1
    let text = ''
    if (action == 'spam') {
      spam_level = 1
      text = 'marked as spam'
    } else if (action == 'approve') {
      spam_level = 0
      text = 'approved'
    } else if (action == 'review') {
      spam_level = -1
      text = 'flagged for review'
    }
    let response = await api.post(`/api/jobs/${job.value.id}/moderate`, { spam_level })
    if (response.status === 200) {

      notification['success']({
        message: 'Success',
        description: `Job post has been ${text}.`,
      })
      fetchJob()
    }
  } catch (error) {
    console.error('Error moderating job:', error)
  } finally {
    modLoading.value = ''
  }
}

onMounted(() => {
  fetchJob()
})
</script>
<template>
  <div class="text-xs-center" v-if="loading">
    <a-spin size="large" :loading="loading" />
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
      <div class="mb-2" v-if="authStore.isAuthenticated">
        <a-tag color="yellow" v-if="job.spam_level == -1">Under Review</a-tag>
        <a-tag color="red" v-if="job.spam_level == 1">Marked as spam</a-tag>
        <a-tag color="green" v-if="job.spam_level == 0">Active</a-tag>
      </div>
      <a-space direction="vertical" style="width: 100%">
        <div><environment-outlined /> {{ job.office }}</div>
        <div><bank-outlined /> {{ job.user?.company ?? job.subcompany }}</div>
        <div><calendar-outlined /> {{ dayjs(job.created_at).format('MMMM D, YYYY hh:mm A') }}</div>
        <div class="mt-2" v-if="job.keywords.length > 0">
          <template v-for="(keyword) in job.keywords">
            <router-link :to="{ name: 'home', query: { tags: keyword.name } }"
              @click="uiStore.activeKeywords = [keyword.name]">
              <a-tag class="mt-2" color="blue">
                {{ keyword.name }}
              </a-tag>
            </router-link>
          </template>
        </div>

        <a-alert v-if="job.spam_level == -1" type="warning" show-icon class="mt-3 mb-0">
          <template #message>
            Under Review
          </template>
          <template #description>
            <div>This post is under review. Until it is approved, it is only visible to the author and to the moderators.</div>
          </template>
        </a-alert>

        <a-alert v-if="authStore.user?.is_moderator" class="mt-2">
          <template #message>Moderator Actions</template>
          <template #description>
            <a-space direction="horizontal" size="small">
              <a-popconfirm title="Are you sure you want to flag this job for review?" @confirm="moderate('review')" v-if="job.spam_level != -1">
                <a-button :loading="modLoading == 'spam'" :disabled="modLoading != ''">Flag for review</a-button>
              </a-popconfirm>

              <a-popconfirm title="Are you sure you want to mark this job as spam?" @confirm="moderate('spam')"
                v-if="job.spam_level != 1">
                <a-button :loading="modLoading == 'spam'" :disabled="modLoading != ''" danger>Mark as spam</a-button>
              </a-popconfirm>

              <a-popconfirm title="Are you sure you want to approve this job?" @confirm="moderate('approve')"
                v-if="job.spam_level != 0">
                <a-button :loading="modLoading == 'approve'" :disabled="modLoading != ''"
                  type="primary">Approve</a-button>
              </a-popconfirm>
            </a-space>
          </template>
        </a-alert>
      </a-space>
      <div v-if="job.job_post_descriptions.length > 0" class="mt-4">
        <a-divider />
        <template v-for="(description, index) in job.job_post_descriptions">
          <div class="mt-4">
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
