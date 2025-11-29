// 2FA is disabled - returning placeholder functions

export const useTwoFactorAuth = () => {
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
