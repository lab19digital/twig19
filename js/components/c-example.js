export const cName = 'c-example'

export default () => {
  $(`.${cName}`).each(function () {
    const c = {}

    c.self = this
    c.$self = $(c.self)
    c.$button = c.$self.find(`.${cName}__button`)

    c.$button.on('click', () => c.$self.toggleClass(`${cName}--primary`))
  })
}
