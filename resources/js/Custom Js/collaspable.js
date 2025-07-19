import {ref} from "vue";

const isCollapsed = ref(false);
 export  function useCollapsedSidebar(){
    return isCollapsed;
}
