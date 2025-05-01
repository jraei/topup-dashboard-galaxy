
/**
 * Composable for managing persistent game account data
 * Uses cookies to store user account information with expiry of 30 days
 */

export const usePersistedAccount = () => {
    const setCookie = (name, value, days = 30) => {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "; expires=" + date.toUTCString();
        document.cookie = name + "=" + encodeURIComponent(JSON.stringify(value)) + expires + "; path=/; SameSite=Lax";
    };

    const getCookie = (name) => {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) {
                try {
                    return JSON.parse(decodeURIComponent(c.substring(nameEQ.length, c.length)));
                } catch (e) {
                    return null;
                }
            }
        }
        return null;
    };

    const saveAccountData = (productSlug, accountData) => {
        // Sanitize inputs before storing
        const sanitizedData = Object.keys(accountData).reduce((obj, key) => {
            obj[key] = String(accountData[key]).replace(/[<>]/g, '');
            return obj;
        }, {});

        setCookie(`game_account_${productSlug}`, {
            product_slug: productSlug,
            account_data: sanitizedData,
            timestamp: new Date().getTime()
        });
        return sanitizedData;
    };

    const getAccountData = (productSlug) => {
        const data = getCookie(`game_account_${productSlug}`);
        return data?.account_data || null;
    };

    const hasStoredAccount = (productSlug) => {
        return !!getAccountData(productSlug);
    };

    const clearAccountData = (productSlug) => {
        document.cookie = `game_account_${productSlug}=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Lax`;
    };

    return {
        saveAccountData,
        getAccountData,
        hasStoredAccount,
        clearAccountData
    };
};
