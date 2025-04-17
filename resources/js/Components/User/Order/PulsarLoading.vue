
<template>
  <div class="pulsar-loading" aria-label="Loading...">
    <div class="pulsar-container">
      <div class="pulsar"></div>
      <div class="pulsar-rings">
        <div class="ring"></div>
        <div class="ring"></div>
        <div class="ring"></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.pulsar-loading {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100px;
}

.pulsar-container {
  position: relative;
  width: 40px;
  height: 40px;
}

.pulsar {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 10px;
  height: 10px;
  background-color: #9b87f5;
  border-radius: 50%;
  transform: translate(-50%, -50%);
  box-shadow: 0 0 10px 2px #9b87f5;
  animation: pulsar-pulse 2s ease-in-out infinite;
}

.pulsar-rings {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.ring {
  position: absolute;
  border: 1px solid #9b87f5;
  border-radius: 50%;
  animation: ring-pulse 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
  opacity: 0;
}

.ring:nth-child(1) {
  width: 20px;
  height: 20px;
  animation-delay: 0s;
}

.ring:nth-child(2) {
  width: 30px;
  height: 30px;
  animation-delay: 0.2s;
}

.ring:nth-child(3) {
  width: 40px;
  height: 40px;
  animation-delay: 0.4s;
}

@keyframes pulsar-pulse {
  0%, 100% {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
  50% {
    opacity: 0.8;
    transform: translate(-50%, -50%) scale(1.2);
  }
}

@keyframes ring-pulse {
  0% {
    transform: translate(-50%, -50%) scale(0.1);
    opacity: 0;
  }
  40% {
    opacity: 0.6;
  }
  100% {
    transform: translate(-50%, -50%) scale(1);
    opacity: 0;
  }
}
</style>
