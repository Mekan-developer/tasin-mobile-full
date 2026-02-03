export const popUpLayoutMixins = {


  data() {
    return {
      isVisiblePopUpLayout: false,
      mounted: false, // управляет монтированием в DOM
    }
  },
  methods: {
    toggleVisiblePopUpLayout() {
       this.mounted = true
      this.$nextTick(() => {
        requestAnimationFrame(() => {
          this.isVisiblePopUpLayout = !this.isVisiblePopUpLayout
        })
      })
    },

    onAfterLeave() {
      this.mounted = false // убираем DOM после завершения анимации
    },
  },
}
