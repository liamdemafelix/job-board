<script setup lang="ts">
import { useUiStore } from '@/stores/ui'
import { ref, watch } from 'vue'

const uiStore = useUiStore()
const emailInputRef = ref<HTMLInputElement | null>(null)

// Auto-focus the email input when the modal is opened
watch(
  () => uiStore.signInModalVisible,
  (visible) => {
    if (visible) {
      setTimeout(() => {
        emailInputRef.value?.focus()
      }, 100)
    }
  }
)
</script>
<template>
  <a-modal v-model:open="uiStore.registerModalVisible" centered :mask-closable="false" :destroy-on-close="true"
    :footer="null" @cancel="uiStore.closeRegistrationModal">
    <a-form :model="uiStore.registrationForm" :colon="false" layout="vertical" @finish="uiStore.register">
      <div style="margin-bottom: 16px;">
        <h3>Welcome!</h3>
        <p>Let's get started. Tell us about yourself.</p>
      </div>
      <a-alert message="Error" type="error" style="margin-bottom: 16px" show-icon
        v-if="uiStore.registrationErrors.length > 0">
        <template #description>
          <div>We had some problems signing you up:</div>
          <ul class="mb-0">
            <li v-for="(error, index) in uiStore.registrationErrors" :key="index">
              {{ error }}
            </li>
          </ul>
          <div class="mt-2">Please resolve these issues and try again.</div>
        </template>
      </a-alert>
      <!-- <a-form-item name="type">
        <a-segmented v-model:value="uiStore.registrationForm.type" block :options="uiStore.accountTypes" />
      </a-form-item> -->
      <a-form-item name="username" v-if="uiStore.registrationForm.type === 'Employer'">
        <a-input v-model:value="uiStore.registrationForm.company_name" size="large" :disabled="uiStore.registerLoading"
          placeholder="Your Company Name" />
      </a-form-item>
      <a-form-item name="email" :rules="[{ required: true, message: 'Please enter your email address.' }]">
        <a-input ref="emailInputRef" v-model:value="uiStore.registrationForm.email" size="large"
          :disabled="uiStore.registerLoading" placeholder="Your Email Address" />
      </a-form-item>
      <a-form-item name="username" :rules="[{ required: true, message: 'Please enter your username.' }]">
        <a-input v-model:value="uiStore.registrationForm.username" size="large" :disabled="uiStore.registerLoading"
          placeholder="Your Username" />
      </a-form-item>
      <a-form-item name="password" :rules="[{ required: true, message: 'Please enter your password.' }]">
        <a-input-password v-model:value="uiStore.registrationForm.password" size="large"
          :disabled="uiStore.registerLoading" placeholder="Your Password" />
      </a-form-item>
      <a-form-item name="password_confirmation" :rules="[{ required: true, message: 'Please enter your password.' }]">
        <a-input-password v-model:value="uiStore.registrationForm.password_confirmation" size="large"
          :disabled="uiStore.registerLoading" placeholder="Confirm Your Password" />
      </a-form-item>
      <a-form-item name="first_name" :rules="[{ required: true, message: 'Please enter your first name.' }]">
        <a-input v-model:value="uiStore.registrationForm.first_name" size="large" :disabled="uiStore.registerLoading"
          placeholder="Your First Name" />
      </a-form-item>
      <a-form-item name="middle_name">
        <a-input v-model:value="uiStore.registrationForm.middle_name" size="large" :disabled="uiStore.registerLoading"
          placeholder="Your Middle Name (optional)" />
      </a-form-item>
      <a-form-item name="last_name" :rules="[{ required: true, message: 'Please enter your last name.' }]">
        <a-input v-model:value="uiStore.registrationForm.last_name" size="large" :disabled="uiStore.registerLoading"
          placeholder="Your Last Name" />
      </a-form-item>
      <a-row>
        <a-col :xs="24" :sm="16" class="text-xs-center text-sm-left">
          <div class="pb-3 pb-sm-0">
            Already have an account? <a @click="uiStore.showSignInModal">Sign in instead</a>.
          </div>
        </a-col>
        <a-col :xs="24" :sm="8" class="text-xs-center text-sm-right">
          <a-space direction="horizontal" size="small">
            <a-button type="primary" html-type="submit" :loading="uiStore.registerLoading">Register</a-button>
          </a-space>
        </a-col>
      </a-row>
    </a-form>
  </a-modal>
</template>
