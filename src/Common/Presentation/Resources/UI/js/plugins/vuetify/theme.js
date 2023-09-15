import { resolveVuetifyTheme } from '@core/utils/vuetify'
import { themeConfig } from '@themeConfig'

export const staticPrimaryColor = '#4066E4'


// console.log(localStorage.getItem("site_theme"),"FUCKINDD")

const theme = {
  defaultTheme: resolveVuetifyTheme(),
  themes: {
    light: {
      dark: false,
      colors: {
        'primary': localStorage.getItem(`${themeConfig.app.title}-lightThemePrimaryColor`) || staticPrimaryColor,
        'on-primary': '#fff',
        'secondary': '#6D788D',
        'on-secondary': '#fff',
        'success': '#48BC65',
        'on-success': '#fff',
        'info': '#26C6F9',
        'on-info': '#fff',
        'warning': '#FDB528',
        'on-warning': '#fff',
        'tiggie-blue' :'#4066E4',
        'candy-red' : '#FF6262',
        'error': '#FF4D49',
        'cancle':'#3749E9',
        'sun-yellow':"#FFCC00",
        'teal':'#17CAB6',
        'background': '#F7F7F9',
        'on-background': '#4c4e64',
        'dark' : '#282828',
        'orange' : '#FF8015',
        'on-surface': '#4c4e64',
        'surface' : '#F6F6F6',
        'inactive-tab' : '#F7F7F7',
        'line' : "#E5E5E5",
        'perfect-scrollbar-thumb': '#DBDADE',
        'sea-form' :'#D7F2F0',
        'snackbar-background': '#212121',
        'tooltip-background': '#262732',
        'alert-background': '#F7F7F9',
        'grey-50': '#FAFAFA',
        'grey-100': '#F4F5FA',
        'grey-200': '#F5F5F7',
        'default-color' :"#606266",
        'pale-blue' : "#D7DDF2",
        'grey-300': '#E0E0E0',
        'grey-400': '#BDBDBD',
        'grey-500': '#9E9E9E',
        'grey-600': '#757575',
        'grey-700': '#616161',
        'grey-800': '#424242',
        'grey-900': '#212121',
        'gray':'#BFC0C1',
        'light': "#F4F4F4",
        'white':'#ffffff'
      },
      variables: {
        'code-color': '#d400ff',
        'border-color': '#4c4e64',
        'hover-opacity': 0.05,
        'overlay-scrim-background': '#4C4E64',
        'overlay-scrim-opacity': 0.5,

        // Shadows
        'shadow-key-umbra-opacity': 'rgba(var(--v-theme-on-surface), 0.08)',
        'shadow-key-penumbra-opacity': 'rgba(var(--v-theme-on-surface), 0.05)',
        'shadow-key-ambient-opacity': 'rgba(var(--v-theme-on-surface), 0.03)',
      },
    },
    dark: {
      dark: true,
      colors: {
        'primary': localStorage.getItem(`${themeConfig.app.title}-darkThemePrimaryColor`) || staticPrimaryColor,
        'on-primary': '#fff',
        'secondary': '#6D788D',
        'on-secondary': '#fff',
        'success': '#72E128',
        'on-success': '#fff',
        'info': '#26C6F9',
        'on-info': '#fff',
        'warning': '#FDB528',
        'on-warning': '#fff',
        'error': '#FF4D49',
        'background': '#282A42',
        'on-background': '#eaeaff',
        'surface': '#30334E',
        'on-surface': '#eaeaff',
        'perfect-scrollbar-thumb': '#4A5072',
        'snackbar-background': '#F5F5F5',
        'on-snackbar-background': '#30334E',
        'tooltip-background': '#464A65',
        'alert-background': '#282A42',
        'grey-50': '#2A2E42',
        'grey-100': '#41435c',
        'grey-200': '#3A3E5B',
        'grey-300': '#5E6692',
        'grey-400': '#7983BB',
        'grey-500': '#8692D0',
        'grey-600': '#AAB3DE',
        'grey-700': '#B6BEE3',
        'grey-800': '#CFD3EC',
        'grey-900': '#E7E9F6',
        'light-states-outlined-resting-border' : '#6D788D'
      },
      variables: {
        'code-color': '#d400ff',
        'border-color': '#eaeaff',
        'hover-opacity': 0.05,
        'overlay-scrim-background': '#101121',
        'overlay-scrim-opacity': 0.6,

        // Shadows
        'shadow-key-umbra-opacity': 'rgba(20, 21, 33, 0.08)',
        'shadow-key-penumbra-opacity': 'rgba(20, 21, 33, 0.05)',
        'shadow-key-ambient-opacity': 'rgba(20, 21, 33, 0.03)',
      },
    },
  },
}

export default theme
