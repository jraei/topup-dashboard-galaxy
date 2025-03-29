
<script setup>
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

// Sample data for popular games (would typically come from props or fetch from backend)
const popularGames = ref([
  { id: 1, name: 'Mobile Legends', orders: 482, trend: 'up', image: '/images/games/ml.jpg' },
  { id: 2, name: 'Free Fire', orders: 346, trend: 'up', image: '/images/games/ff.jpg' },
  { id: 3, name: 'PUBG Mobile', orders: 291, trend: 'down', image: '/images/games/pubg.jpg' },
  { id: 4, name: 'Genshin Impact', orders: 187, trend: 'up', image: '/images/games/genshin.jpg' },
  { id: 5, name: 'Call of Duty Mobile', orders: 154, trend: 'down', image: '/images/games/codm.jpg' },
]);
</script>

<template>
  <Card class="bg-gradient-to-br from-dark-card to-dark-lighter border-gray-700 hover:shadow-card-glow transition-all h-full">
    <CardHeader class="pb-2">
      <CardTitle class="text-lg font-medium text-white">Popular Games</CardTitle>
    </CardHeader>
    <CardContent>
      <div class="space-y-4">
        <div v-for="game in popularGames" :key="game.id" class="flex items-center justify-between p-3 rounded-lg bg-dark-lighter bg-opacity-50 hover:bg-opacity-70 transition-all">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-md bg-dark-deepest flex items-center justify-center overflow-hidden">
              <!-- Fallback icon if image is not available -->
              <svg v-if="!game.image" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
              </svg>
              <img v-else :src="game.image" alt="Game logo" class="w-full h-full object-cover" />
            </div>
            <div>
              <p class="text-white font-medium">{{ game.name }}</p>
              <p class="text-xs text-gray-400">{{ game.orders }} orders</p>
            </div>
          </div>
          <Badge :class="[
            game.trend === 'up' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400',
            'flex items-center space-x-1'
          ]">
            <span v-if="game.trend === 'up'" class="i-lucide-trending-up w-3 h-3 mr-1"></span>
            <span v-else class="i-lucide-trending-down w-3 h-3 mr-1"></span>
            <span>{{ game.trend }}</span>
          </Badge>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
