<template>
  <div class="pakui-logo-cube">
    <div class="pakui-cube">
      <div class="pakui-face-front" />
      <div class="pakui-face-back" />
      <div class="pakui-face-right" />
      <div class="pakui-face-left" />
      <div class="pakui-face-top" />
      <div class="pakui-face-bottom" />
    </div>
  </div>
</template>

<style scoped>
.pakui-cube {
  --cube-face-width: 96px;
  animation: rotate 4s infinite linear;
  height: var(--cube-face-width);
  position: relative;
  transform-style: preserve-3d;
  width: var(--cube-face-width);
}

div[class*="pakui-face-"] {
  animation: colors 2s infinite linear;
  background-color: transparent;
  background-image: url('../assets/logo.png');
  background-size: cover;
  border: 1px solid transparent;
  height: var(--cube-face-width);
  position: absolute;
  width: var(--cube-face-width);
}

.pakui-face-front {
  transform: translateZ(calc(var(--cube-face-width) / 2));
}

.pakui-face-back {
  transform: rotateY(180deg) translateZ(calc(var(--cube-face-width) / 2));
}

.pakui-face-right {
  transform: rotateY(90deg) translateZ(calc(var(--cube-face-width) / 2));
}

.pakui-face-left {
  transform: rotateY(-90deg) translateZ(calc(var(--cube-face-width) / 2));
}

.pakui-face-top {
  transform: rotateX(90deg) translateZ(calc(var(--cube-face-width) / 2));
}

.pakui-face-bottom {
  transform: rotateX(-90deg) translateZ(calc(var(--cube-face-width) / 2));
}

@keyframes rotate {
  0% {
    transform: rotateX(0deg) rotateY(0deg);
  }
  100% {
    transform: rotateX(360deg) rotateY(360deg);
  }
}

@keyframes colors {
  0% {
    background-color: var(--pakui-color-primary);
    border-color: var(--pakui-color-primary);
  }
  50% {
    background-color: var(--pakui-color-highlight);
    border-color: var(--pakui-color-highlight);
  }
  100% {
    background-color: var(--pakui-color-primary);
    border-color: var(--pakui-color-primary);
  }
}
</style>
