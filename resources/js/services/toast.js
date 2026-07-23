import { reactive } from 'vue';

export const toastState = reactive({
  toasts: []
});

export const toast = {
  show(message, type = 'success', duration = 4000) {
    const id = Date.now() + Math.random().toString(36).substring(2, 9);
    toastState.toasts.push({ id, message, type });
    setTimeout(() => {
      this.remove(id);
    }, duration);
  },
  remove(id) {
    const index = toastState.toasts.findIndex(t => t.id === id);
    if (index !== -1) {
      toastState.toasts.splice(index, 1);
    }
  },
  success(message, duration) {
    this.show(message, 'success', duration);
  },
  error(message, duration) {
    this.show(message, 'error', duration);
  },
  info(message, duration) {
    this.show(message, 'info', duration);
  },
  warning(message, duration) {
    this.show(message, 'warning', duration);
  }
};
