<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import { useUiStore } from '@/stores/ui'
import { SaveOutlined, PlusOutlined, DeleteOutlined } from '@ant-design/icons-vue'
import { notification } from 'ant-design-vue'

const router = useRouter()
const uiStore = useUiStore()
uiStore.sidebarSelectedKeys = ['new_job']

const loading = ref<boolean>(true)
const allowed = ref<boolean>(false)
const jobFormLoading = ref<boolean>(false)
const jobForm = ref<any>({
  name: '',
  office: '',
  department: '',
  employment_type: '',
  seniority: '',
  schedule: '',
  years_of_experience: '',
  occupation: '',
  occupation_category: '',
  keywords: [],
  descriptions: [{ title: '', content: '' }]
})

onMounted(async () => {
  let userRequest = await api.get('/api/user')
  let user = userRequest.data
  if (user.company == '' || user.company == null) {
    allowed.value = false
  } else {
    allowed.value = true
  }
  loading.value = false
})

const addDescriptionField = () => {
  jobForm.value.descriptions.push({ title: '', content: '' })
}

const removeDescriptionField = (index: number) => {
  if (index === 0) return // Cannot remove the first pair
  jobForm.value.descriptions.splice(index, 1)
}

function submitForm() {
  jobFormLoading.value = true
  api.post('/api/jobs', jobForm.value)
    .then((response) => {
      jobFormLoading.value = false
      let data = response.data.data
      if (data.spam_level == -1) {
        notification['warning']({
          message: 'Warning',
          description: 'Your job post has been saved. Since this is your first job post, it will be reviewed by an admin before being published.',
        })
      } else {
        notification['success']({
          message: 'Success',
          description: 'Job posted successfully.',
        })
      }
      router.push({ name: 'job', params: { id: data.id } })
    })
    .catch((error) => {
      jobFormLoading.value = false
      notification['error']({
        message: 'Error',
        description: 'An error occurred while posting the job. Please try again.',
      })
      console.error(error)
    })
}

</script>
<template>
  <div class="px-3">
    <a-typography>
      <a-typography-title>Post New Job</a-typography-title>
    </a-typography>
    <a-divider />
    <div class="text-center" v-if="loading">
      <a-spin size="large" />
    </div>
    <div v-else-if="!allowed">
      <p>Sorry, you are not allowed to perform this action.</p>
    </div>
    <div v-else>
      <a-form layout="vertical" :colon="false" :model="jobForm" @finish="submitForm">
        <a-row :gutter="[16, 0]">
          <a-col :span="24">
            <a-form-item label="Job Title" name="name"
              :rules="[{ required: true, message: 'Please enter a job title.' }]">
              <a-input placeholder="e.g. Senior Software Engineer" v-model:value="jobForm.name" :disabled="jobFormLoading" />
            </a-form-item>
          </a-col>
          <a-col :span="24">
            <a-form-item label="Office" name="office"
              :rules="[{ required: true, message: 'Please enter an office location.' }]">
              <a-input placeholder="e.g. New York" v-model:value="jobForm.office" :disabled="jobFormLoading" />
            </a-form-item>
          </a-col>
          <a-col :span="24">
            <a-form-item label="Department" name="department"
              :rules="[{ required: true, message: 'Please enter a department.' }]">
              <a-input placeholder="e.g. Engineering" v-model:value="jobForm.department" :disabled="jobFormLoading" />
            </a-form-item>
          </a-col>
          <a-col :xs="24" :lg="8">
            <a-form-item label="Employment Type" name="employment_type"
              :rules="[{ required: true, message: 'Please enter an employment type.' }]">
              <a-input placeholder="e.g. Full-time" v-model:value="jobForm.employment_type" :disabled="jobFormLoading" />
            </a-form-item>
          </a-col>
          <a-col :xs="24" :lg="8">
            <a-form-item label="Seniority" name="seniority"
              :rules="[{ required: true, message: 'Please enter a seniority level.' }]">
              <a-input placeholder="e.g. Senior Level" v-model:value="jobForm.seniority" :disabled="jobFormLoading" />
            </a-form-item>
          </a-col>
          <a-col :xs="24" :lg="8">
            <a-form-item label="Schedule" name="schedule"
              :rules="[{ required: true, message: 'Please enter a schedule.' }]">
              <a-input placeholder="e.g. Day Shift" v-model:value="jobForm.schedule" :disabled="jobFormLoading" />
            </a-form-item>
          </a-col>
          <a-col :xs="24" :lg="8">
            <a-form-item label="Years of Experience" name="years_of_experience"
              :rules="[{ required: true, message: 'Please enter years of experience.' }]">
              <a-input placeholder="e.g. 5" v-model:value="jobForm.years_of_experience" :disabled="jobFormLoading" />
            </a-form-item>
          </a-col>
          <a-col :xs="24" :lg="8">
            <a-form-item label="Occupation" name="occupation"
              :rules="[{ required: true, message: 'Please enter an occupation.' }]">
              <a-input placeholder="e.g. Software Developer" v-model:value="jobForm.occupation" :disabled="jobFormLoading" />
            </a-form-item>
          </a-col>
          <a-col :xs="24" :lg="8">
            <a-form-item label="Occupation Category" name="occupation_category"
              :rules="[{ required: true, message: 'Please enter an occupation category.' }]">
              <a-input placeholder="e.g. Information Technology" v-model:value="jobForm.occupation_category" :disabled="jobFormLoading" />
            </a-form-item>
          </a-col>
            <a-col :span="24">
            <a-form-item
              label="Keywords"
              name="keywords"
              :rules="[{ required: true, message: 'Please enter at least one keyword.', type: 'array', min: 1 }]"
            >
              <a-select
              v-model:value="jobForm.keywords"
              mode="tags"
              placeholder="Select or enter keywords"
              :tokenSeparators="[',']"
              :disabled="jobFormLoading"
              >
              </a-select>
            </a-form-item>
            </a-col>

          <a-col :span="24">
            <div class="descriptions-section">
              <div class="descriptions-header">
                <h3>Descriptions</h3>
                <a-button type="primary" @click="addDescriptionField">
                  <plus-outlined /> Add Section
                </a-button>
              </div>

              <div v-for="(description, index) in jobForm.descriptions" :key="index" class="description-item">
                <div class="description-header">
                  <h4>Section {{ index + 1 }}</h4>
                  <a-button
                    v-if="index !== 0"
                    type="text"
                    danger
                    @click="removeDescriptionField(index)"
                    :disabled="jobFormLoading"
                  >
                    <delete-outlined /> Remove
                  </a-button>
                </div>

                <a-form-item
                  :name="['descriptions', index, 'title']"
                  :rules="[{ required: true, message: 'Please enter a section title.' }]"
                  class="mb-2"
                >
                  <a-input
                    placeholder="Section Title"
                    v-model:value="description.title"
                    :disabled="jobFormLoading"
                  />
                </a-form-item>

                <a-form-item
                  :name="['descriptions', index, 'content']"
                  :rules="[{ required: true, message: 'Please enter section content.' }]"
                  class="mb-0"
                >
                  <a-textarea
                    placeholder="Section Content"
                    v-model:value="description.content"
                    :rows="4"
                    :disabled="jobFormLoading"
                  />
                </a-form-item>
              </div>
            </div>
          </a-col>

          <a-col :span="24" class="text-xs-right">
            <a-form-item>
              <a-button type="primary" html-type="submit" size="large" :loading="jobFormLoading"><save-outlined v-if="!jobFormLoading" /> Save</a-button>
            </a-form-item>
          </a-col>
        </a-row>
      </a-form>
    </div>
  </div>
</template>
