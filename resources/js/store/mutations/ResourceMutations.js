/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */
import findIndex from 'lodash/findIndex'
import merge from 'lodash/merge'

export default {
  /**
   * Set single record data in store
   *
   * @param {Object} state
   * @param {Object} record
   */
  SET_RECORD(state, data) {
    state.record = Object.assign({}, state.record, data)
  },

  /**
   * Reset single record
   *
   * @param {Object} state
   */
  RESET_RECORD(state) {
    state.record = {}
  },

  /**
   * Reset the record has many relationship
   *
   * @param {Object} state
   * @param {String} relation
   */
  RESET_RECORD_HAS_MANY_RELATIONSHIP(state, relation) {
    state.record[relation] = []
  },

  /**
   * Set record has many relationships
   *
   * @param {Object} state
   * @param {Object} data
   */
  SET_RECORD_HAS_MANY_RELATIONSHIP(state, data) {
    state.record[data.relation] = data.items
  },

  /**
   * Add record has many relationship
   *
   * @param {Object} state
   * @param {Object} data
   */
  ADD_RECORD_HAS_MANY_RELATIONSHIP(state, data) {
    if (!state.record[data.relation]) {
      state.record[data.relation] = []
    }

    if (data.force === true) {
      state.record[data.relation].push(data.item)
    } else {
      const existingIndex = findIndex(state.record[data.relation], [
        'id',
        Number(data.item.id),
      ])

      if (existingIndex === -1) {
        state.record[data.relation].push(data.item)
      }
    }
  },

  /**
   * Update record has many relationship
   *
   * @param {Object} state
   * @param {Object} data
   */
  UPDATE_RECORD_HAS_MANY_RELATIONSHIP(state, data) {
    const index = findIndex(state.record[data.relation], [
      'id',
      Number(data.id),
    ])

    if (index !== -1) {
      state.record[data.relation][index] = merge(
        state.record[data.relation][index],
        data.item
      )
    }
  },

  /**
   * Remove record has many relationship
   *
   * @param {Object} state
   * @param {Object} data
   */
  REMOVE_RECORD_HAS_MANY_RELATIONSHIP(state, data) {
    const index = findIndex(state.record[data.relation], [
      'id',
      Number(data.id),
    ])

    if (index != -1) {
      state.record[data.relation].splice(index, 1)
    }
  },

  /**
   * Add relation to the given record has many relation
   *
   * @param {Object} state
   * @param {Object} data
   */
  ADD_RECORD_HAS_MANY_SUB_RELATION(state, data) {
    let index = findIndex(state.record[data.relation], [
      'id',
      Number(data.relation_id),
    ])

    if (index != -1) {
      if (
        !state.record[data.relation][index].hasOwnProperty(data.sub_relation)
      ) {
        state.record[data.relation][index][data.sub_relation] = []
      }

      state.record[data.relation][index][data.sub_relation].push(data.item)
    }
  },

  /**
   * Update the given has many relation to from the record has many relation
   *
   * @param {Object} state
   * @param {Object} data
   */
  UPDATE_RECORD_HAS_MANY_SUB_RELATION(state, data) {
    let mainRelationIndex = findIndex(state.record[data.relation], [
      'id',
      Number(data.relation_id),
    ])

    if (mainRelationIndex !== -1) {
      let subRelationIndex = findIndex(
        state.record[data.relation][mainRelationIndex][data.sub_relation],
        ['id', Number(data.sub_relation_id)]
      )

      if (mainRelationIndex != -1 && subRelationIndex != -1) {
        state.record[data.relation][mainRelationIndex][data.sub_relation][
          subRelationIndex
        ] = merge(
          state.record[data.relation][mainRelationIndex][data.sub_relation][
            subRelationIndex
          ],
          data.item
        )
      }
    }
  },

  /**
   * Remove a has many sub relation from a record has many relation
   *
   * @param {Object} state
   * @param {Object} data
   */
  REMOVE_RECORD_HAS_MANY_SUB_RELATION(state, data) {
    let mainRelationIndex = findIndex(state.record[data.relation], [
      'id',
      Number(data.relation_id),
    ])

    let subRelationIndex = findIndex(
      state.record[data.relation][mainRelationIndex][data.sub_relation],
      ['id', Number(data.sub_relation_id)]
    )

    if (mainRelationIndex != -1 && subRelationIndex != -1) {
      state.record[data.relation][mainRelationIndex][data.sub_relation].splice(
        subRelationIndex,
        1
      )
    }
  },
}
