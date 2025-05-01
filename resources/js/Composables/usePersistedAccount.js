
import { ref, watch } from 'vue';

export function usePersistedAccount() {
    const savedAccounts = ref({});

    // Load saved accounts from cookies on startup
    const loadSavedAccounts = () => {
        try {
            const accountsStr = document.cookie.split('; ')
                .find(row => row.startsWith('game_accounts='));
            
            if (accountsStr) {
                const cookieValue = accountsStr.split('=')[1];
                if (cookieValue) {
                    savedAccounts.value = JSON.parse(decodeURIComponent(cookieValue)) || {};
                }
            }
        } catch (error) {
            console.error('Error loading saved accounts:', error);
            // Reset to empty object if there's an issue
            savedAccounts.value = {};
        }
    };

    // Save account data to cookie
    const saveAccount = (productSlug, accountData) => {
        if (!productSlug || !accountData) return false;
        
        try {
            // Sanitize inputs to prevent XSS
            const sanitizedProductSlug = String(productSlug).replace(/[^\w-]/g, '');
            const sanitizedAccountData = {};
            
            // Only copy specific fields we expect and sanitize them
            if (accountData.user_id) {
                sanitizedAccountData.user_id = String(accountData.user_id).replace(/[<>]/g, '');
            }
            if (accountData.server_id || accountData.input_zone) {
                sanitizedAccountData.server_id = String(accountData.server_id || accountData.input_zone).replace(/[<>]/g, '');
            }
            
            // Update local state
            savedAccounts.value = {
                ...savedAccounts.value,
                [sanitizedProductSlug]: { account_data: sanitizedAccountData }
            };
            
            // Save to cookie with 30 day expiry
            const expiryDate = new Date();
            expiryDate.setDate(expiryDate.getDate() + 30);
            
            document.cookie = `game_accounts=${encodeURIComponent(JSON.stringify(savedAccounts.value))}; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;
            
            return true;
        } catch (error) {
            console.error('Error saving account:', error);
            return false;
        }
    };

    // Get saved account data for a specific product
    const getAccountForProduct = (productSlug) => {
        if (!productSlug || !savedAccounts.value[productSlug]) return null;
        return savedAccounts.value[productSlug].account_data || null;
    };

    // Check if there's saved data for a product
    const hasSavedAccount = (productSlug) => {
        return !!getAccountForProduct(productSlug);
    };

    // Load on initialization
    loadSavedAccounts();

    return {
        savedAccounts,
        saveAccount,
        getAccountForProduct,
        hasSavedAccount
    };
}
