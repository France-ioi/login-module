import { useRouterHistory, Router, Route } from 'react-router'
import { createHistory } from 'history'

var history = useRouterHistory(createHistory)({ basename: '/' })

export function getHistory() {
    return history
}