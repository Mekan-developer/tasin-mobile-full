<template>
  <div class="w-full flex justify-end">
    <button class="bg-my-gradient hover:bg-my-gradient-reverse hover:scale-102 active:scale-98 text-white font-medium py-3 px-8 rounded-sm transition duration-300">{{ buttonText }}</button>
  </div>
</template>

<script>
export default {
  name: "SubmitButton",
  props: {
    buttonText: { type: String, default: "Submit" }
  }

}

</script>
