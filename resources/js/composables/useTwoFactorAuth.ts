// 2FA is disabled - returning placeholder functions
import { computed, ref } from 'vue';

export const useTwoFactorAuth = () => {
    const isEnabled = ref(false);
    const qrCode = ref('');
    const recoveryCodes = ref<string[]>([]);
    const secretKey = ref('');

    const enable = async () => {
        // 2FA is disabled
        return Promise.resolve();
    };

    const disable = async () => {
        // 2FA is disabled
        return Promise.resolve();
    };

    const regenerateRecoveryCodes = async () => {
        // 2FA is disabled
        return Promise.resolve();
    };

    return {
        isEnabled: computed(() => false),
        qrCode: computed(() => ''),
        recoveryCodes: computed(() => []),
        secretKey: computed(() => ''),
        enable,
        disable,
        regenerateRecoveryCodes,
    };
};
