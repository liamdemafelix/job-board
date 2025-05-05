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
  <a-modal v-model:open="uiStore.signInModalVisible" centered :mask-closable="false" :destroy-on-close="true"
    :footer="null" @cancel="uiStore.closeSignInModal">
    <a-form :model="uiStore.signInForm" :colon="false" layout="vertical" @finish="uiStore.signIn">
      <div style="margin-bottom: 16px;">
        <h3>Welcome back!</h3>
        <p>To continue, please sign in.</p>
      </div>
      <a-form-item name="email" :rules="[{ required: true, message: 'Please enter your username or email address.' }]">
        <a-input ref="emailInputRef" v-model:value="uiStore.signInForm.email" size="large"
          :disabled="uiStore.signInLoading" placeholder="Your Email Address or Username" />
      </a-form-item>
      <a-form-item name="password" :rules="[{ required: true, message: 'Please enter your password.' }]">
        <a-input-password v-model:value="uiStore.signInForm.password" size="large" :disabled="uiStore.signInLoading"
          placeholder="Your Password" />
      </a-form-item>
      <a-row>
        <a-col :xs="24" :sm="16" class="text-xs-center text-sm-left">
          <div class="pb-3 pb-sm-0">
            Don't have an account? <a @click="uiStore.showRegistrationModal">Create one now</a>.
          </div>
        </a-col>
        <a-col :xs="24" :sm="8" class="text-xs-center text-sm-right">
          <a-space direction="horizontal" size="small">
            <a-button type="primary" html-type="submit" :loading="uiStore.signInLoading">Sign
              In</a-button>
          </a-space>
        </a-col>
      </a-row>
    </a-form>
  </a-modal>
</template>
