<script setup>
import { onMounted, ref } from 'vue'
import { fetchEmployerApplications, payApplication, reviewApplication } from '@/services/jobsService'

const applications = ref([])
const loading = ref(true)
const message = ref('')
const error = ref('')
const selectedPayment = ref({})

async function loadApplications() {
  loading.value = true
  error.value = ''
  const response = await fetchEmployerApplications()

  if (response.ok) {
    applications.value = response.data
  }

  loading.value = false
}

onMounted(loadApplications)

function setPaymentDraft(application) {
  selectedPayment.value = {
    ...selectedPayment.value,
    [application.id]: {
      provider: selectedPayment.value[application.id]?.provider || 'stripe',
      amount: selectedPayment.value[application.id]?.amount || application.jobListing?.salary_min || 0,
      currency: 'USD',
      reference: '',
    },
  }
}

async function handleReview(applicationId, action) {
  const response = await reviewApplication(applicationId, action)

  if (response.ok) {
    message.value = action === 'approve' ? 'Application approved successfully.' : 'Application rejected successfully.'
    await loadApplications()
  } else {
    error.value = response.message || 'Unable to update application.'
  }
}

async function handlePay(application) {
  const paymentData = selectedPayment.value[application.id] || {
    provider: 'stripe',
    amount: application.jobListing?.salary_min || 0,
    currency: 'USD',
    reference: '',
  }
  const response = await payApplication(application.id, paymentData)

  if (response.ok) {
    message.value = 'Payment recorded successfully.'
    await loadApplications()
  } else {
    error.value = response.message || 'Unable to record payment.'
  }
}
</script>

<template>
  <div class="container-fluid py-4 px-3 px-md-4">
    <div class="dashboard-container">
      <div class="p-4 border-bottom flex justify-content-between align-items-center">
        <div>
          <h2 class="fs-5 fw-bold mb-0 text-dark">Applications</h2>
          <p class="small text-muted mb-0">Approve candidates, then record Stripe or PayPal payment.</p>
        </div>
      </div>

      <div class="p-4" v-if="message || error">
        <div v-if="message" class="alert alert-success mb-2">{{ message }}</div>
        <div v-if="error" class="alert alert-danger mb-0">{{ error }}</div>
      </div>

      <div v-if="loading" class="p-5 text-center text-muted">Loading applications...</div>
      <div v-else-if="applications.length === 0" class="p-5 text-center text-muted">
        No applications yet.
      </div>
      <div v-else class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-start">
          <thead>
            <tr class="table-light border-bottom">
              <th class="px-4 py-3 small fw-semibold text-muted">Candidate</th>
              <th class="px-4 py-3 small fw-semibold text-muted">Job</th>
              <th class="px-4 py-3 small fw-semibold text-muted">Status</th>
              <th class="px-4 py-3 small fw-semibold text-muted">Payment</th>
              <th class="px-4 py-3 small fw-semibold text-muted text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="application in applications" :key="application.id">
              <td class="px-4 py-3">
                <p class="fw-semibold mb-0 text-dark">{{ application.candidate_name }}</p>
                <p class="small text-muted mb-0">{{ application.email }}</p>
              </td>
              <td class="px-4 py-3">
                <p class="fw-semibold mb-0 text-dark">{{ application.jobListing?.title }}</p>
                <p class="small text-muted mb-0">{{ application.jobListing?.company }}</p>
              </td>
              <td class="px-4 py-3 fw-semibold text-capitalize">{{ application.status }}</td>
              <td class="px-4 py-3">
                <template v-if="application.paymentTransaction">
                  {{ application.paymentTransaction.provider }} - {{ application.paymentTransaction.status }}
                </template>
                <template v-else>
                  <div class="d-flex flex-column gap-2" style="min-width: 220px">
                    <select
                      class="form-select form-select-sm"
                      :value="selectedPayment[application.id]?.provider || 'stripe'"
                      @change="selectedPayment[application.id] = { ...(selectedPayment[application.id] || {}), provider: $event.target.value, amount: selectedPayment[application.id]?.amount || application.jobListing?.salary_min || 0, currency: 'USD', reference: selectedPayment[application.id]?.reference || '' }"
                    >
                      <option value="stripe">Stripe</option>
                      <option value="paypal">PayPal</option>
                    </select>
                    <input
                      class="form-control form-control-sm"
                      type="number"
                      min="0"
                      step="0.01"
                      :value="selectedPayment[application.id]?.amount || application.jobListing?.salary_min || 0"
                      @input="selectedPayment[application.id] = { ...(selectedPayment[application.id] || {}), amount: Number($event.target.value), provider: selectedPayment[application.id]?.provider || 'stripe', currency: 'USD', reference: selectedPayment[application.id]?.reference || '' }"
                    />
                  </div>
                </template>
              </td>
              <td class="px-4 py-3 text-end">
                <div class="d-flex justify-content-end gap-2 flex-wrap">
                  <button
                    v-if="application.status !== 'approved'"
                    class="btn btn-sm btn-dark"
                    @click="handleReview(application.id, 'approve')"
                  >
                    Approve
                  </button>
                  <button
                    v-if="application.status !== 'rejected'"
                    class="btn btn-sm btn-outline-danger"
                    @click="handleReview(application.id, 'reject')"
                  >
                    Disapprove
                  </button>
                  <button
                    v-if="application.status === 'approved' && !application.paymentTransaction"
                    class="btn btn-sm btn-success"
                    @click="handlePay(application)"
                  >
                    Pay
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>