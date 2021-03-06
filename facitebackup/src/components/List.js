import React from "react";
import FaciteCard from "./FaciteCard";
import FaciteActionButton from "./FaciteActionButton"
import { Droppable, Draggable } from "react-beautiful-dnd";
import styled from "styled-components";

//Style list
const ListContainer = styled.div`
background-color: #dfe4e6;
border-radius: 3px;
width: 300px;
padding: 8px;
height: 100%;
margin-right: 8px;
`

const List  = ({title, cards, listID, index}) => {
    console.log(cards);
    return (
        <Draggable draggableId={String (listID)} index={index}>
        {provided => (
            <ListContainer
            {...provided.draggableProps} 
            ref={provided.innerRef} 
            {...provided.dragHandleProps}>
            <Droppable droppableId={String (listID)}>
            {provided => (
                <div {...provided.droppableProps} ref={provided.innerRef}>
                 <h4> {title} </h4>
                 { cards.map((card, index) => (
                  <FaciteCard 
                  key={card.id} 
                  index={index} 
                  text={card.text} 
                  id={card.id} />
                  ))}
                  {provided.placeholder}
                  <FaciteActionButton listID={listID}/>
                  </div>
            )}
            </Droppable>
            </ListContainer>
        )}
        </Draggable>
    );
};


export default List;